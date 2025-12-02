<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VoidsModel;
use App\Imports\VoidsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class VoidsController extends Controller
{



public function index(Request $request)
{
    $user = Session::get('user');
    $permissions = Session::get('permissions');
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_voids')))) {
        return redirect()->back()->with('error', 'Unauthorized access to voids.');
    }   
    // Get today's date and one year ago
    $today = Carbon::now();
    $oneYearAgo = $today->copy()->subYear();

    // Fetch only voids from the past year
    $voids = VoidsModel::whereDate('termination_date', '>=', $oneYearAgo)
        ->whereDate('termination_date', '<=', $today)
        ->get();

    // Update days_void only if it's outdated
    foreach ($voids as $void) {
        $calculatedDays = Carbon::parse($void->termination_date)->diffInDays($today);
        if ($calculatedDays > $void->days_void) {
            $void->update(['days_void' => $calculatedDays]);
        }
    }

    return view('layouts.voids.index', compact('voids'));
}
    public function create()
    {
        return view('layouts.voids.create');
    }

public function store(Request $request)
{
    $user = Session::get('user');
    $permissions = Session::get('permissions'); 
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_voids')))) {
        return redirect()->back()->with('error', 'Unauthorized access to voids.');
    }
    // Clean and merge input before validation
    $input = $request->all();
    foreach ($input as $key => $value) {
        if (is_string($value)) {
            $input[$key] = strip_tags(trim($value));
        }
    }
    $request->merge($input);

    // Now validate
    $request->validate([
        'file' => 'required_without:void_path|file|mimes:xlsx,xls,csv|max:10240',

        'void_path' => 'required_without:file|string',
        'void_classification' => 'required_without:file|string',
        'hfi_code' => 'required_without:file|string',
        'uprn' => 'required_without:file|string',
        'property_ref' => 'required_without:file|string',
        'ten_reason' => 'nullable|string',
        'void_ref' => 'nullable|string',
        'address' => 'required_without:file|string',
        'updates'              => 'nullable|string',
        'previous_call_over'   => 'nullable|string',
        'property_type' => 'nullable|string',
        'property_subtype' => 'nullable|string',
        'bedrooms' => 'nullable|integer|min:0',
        'void_status' => 'nullable|string',
        'vin_sco_code' => 'nullable|string',
        'days_void' => 'nullable|integer|min:0',
        'termination_date' => 'nullable|date',
        'ready_for_let_date' => 'nullable|date',
        'management_unit' => 'nullable|string',
    ]);

if ($request->hasFile('file')) {
    $file = $request->file('file');

    try {
        // Save file to storage/app/imports/
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $relativePath = $file->storeAs('imports', $filename, 'local');
        $absolutePath = \Storage::disk('local')->path($relativePath);


        // Optional: Clear stat cache and wait to ensure file system sync
        clearstatcache();
        sleep(1); // Sometimes needed on shared hosting or slow disks

        // Debugging log
        \Log::info('Starting import', [
            'relative_path' => $relativePath,
            'absolute_path' => $absolutePath,
            'file_exists' => file_exists($absolutePath),
        ]);

        // Double-check file existence
        if (!file_exists($absolutePath)) {
            return response()->json([
                'message' => 'Uploaded file not found on disk.',
                'status' => 'error',
                'path' => $absolutePath,
            ], 500);
        }

        // Proceed with import
       Excel::import(new VoidsImport($absolutePath), $absolutePath);


        return response()->json([
            'message' => 'All sheets imported successfully!',
            'status' => 'success',
            'success' => true,
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Failed to import sheets.',
            'status' => 'error',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    // Manual form submission
    $cleaned = $request->except('file');
   $cleaned['void_ref'] = VoidsModel::generateVoidRef();

if (!empty($cleaned['termination_date'])) {
    $terminationDate = Carbon::parse($cleaned['termination_date']);
    $cleaned['termination_date'] = $terminationDate;

    $cleaned['days_void'] = ceil($terminationDate->isPast()
        ? $terminationDate->diffInDays(now())
        : 0);
} else {
    $cleaned['days_void'] = null;
}


   $data = VoidsModel::create($cleaned);

    return redirect()->route('void.index')->with('success', 'Void record created successfully!');
}



    public function show()
    {
        
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_voids')))) {
            return redirect()->back()->with('error', 'Unauthorized access to voids.');
        }
        return view('layouts.voids.create');
    }

    public function edit($id)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions'); 
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_voids')))) {
            return redirect()->back()->with('error', 'Unauthorized access to voids.');
        }
        
        $void = VoidsModel::findOrFail($id);
        return view('layouts.voids.edit', compact('void'));
    }

public function update(Request $request, $id)
{
    $user = Session::get('user');
    $permissions = Session::get('permissions'); 
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_voids')))) {
        return redirect()->back()->with('error', 'Unauthorized access to voids.');
    }
    // Validate all fields
    $validatedData = $request->validate([
        'void_path'            => 'required|string',
        'void_classification'  => 'required|string',
        'hfi_code'             => 'required|string',
        'uprn'                 => 'required|string',
        'property_ref'         => 'required|string',
        'ten_reason'           => 'nullable|string',
        'void_ref'             => 'nullable|string',
        'address'              => 'required|string',
        'updates'              => 'nullable|string',
        'previous_call_over'   => 'nullable|string',
        'property_type'        => 'nullable|string',
        'property_subtype'     => 'nullable|string',
        'bedrooms'             => 'nullable|integer',
        'void_status'          => 'nullable|string',
        'vin_sco_code'         => 'nullable|string',
        'days_void'            => 'nullable|integer',
        'termination_date'     => 'nullable|date',
        'ready_for_let_date'   => 'nullable|date',
        'management_unit'      => 'nullable|string',
    ]);

    // Find the void record
    $void = VoidsModel::findOrFail($id);

    // Update the void record with validated data
    $void->update($validatedData);

    // Redirect with success message
    return redirect()->route('void.index')->with('success', 'Void updated successfully!');
}


    public function destroy($id)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions'); 
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'delete_voids')))) {
            return redirect()->back()->with('error', 'Unauthorized access to voids.');
        }
        $void = VoidsModel::findOrFail($id);
        $void->delete();

        return redirect()->route('void.index')->with('success', 'Void deleted successfully!');
    }

    public function VoidImport()
    {
        return view('layouts.voids.import');
    }
}
