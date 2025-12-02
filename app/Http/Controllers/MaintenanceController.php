<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\RepairsImports;
use App\Models\Repairs;
use App\Models\BlockModel;
use Illuminate\Support\Facades\Session;
class MaintenanceController extends Controller
{
 
    public function import(Request $request){
    $user = Session::get('user');
    $permissions = Session::get('permissions');
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_maintenance')))) {
        // Return a JSON response indicating unauthorized access    
        return response()->json([
            'message' => 'Unauthorized access to import repairs.',
            'status' => 'error',
            'success' => false,
        ], 403);
    }
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv|max:10240', // max size 10MB
    ]);

    try {
        // Perform the import
       Excel::import(new RepairsImports, $request->file('file'));
        // Return a JSON response indicating success
        return response()->json([
            'message' => 'maintenance imported successfully!',
            'status' => 'success',
            'success'=>true,
        ], 201);
    } catch (\Exception $e) {
        // Return a JSON response indicating failure
        return response()->json([
            'message' => 'Failed to import maintenance.',
            'status' => 'error',
            'error' => $e->getMessage(),
        ], 500);
    }
         
        
    }
     public function getImport(Request $request){
    $user = Session::get('user');
    $permissions = Session::get('permissions'); 
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_maintenance')))) {
        return redirect()->back()->with('error', 'Unauthorized access to import repairs.');
    }
    return view('layouts.repairs.import');
    }
  public function index()
{
    $user = Session::get('user');
    $permissions = Session::get('permissions'); 
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_maintenance')))) {
        return redirect()->back()->with('error', 'Unauthorized access to repairs.');
    }
    $repairs = Repairs::with('block:id,name')->get();
    return view('layouts.repairs.index', compact('repairs')); 
}


    public function search(Request $request){
        dd($request);
        
    }
    public function LoadCreate(){
    $user = Session::get('user');
    $permissions = Session::get('permissions');
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_maintenance')))) {
        return redirect()->back()->with('error', 'Unauthorized access to create repairs.');
    }
     $blocks = json_encode(BlockModel::with(['apartments:id,block_models_id,address,unit_number'])
    ->select('id', 'name')
    ->get());
       return view('layouts.repairs.create', compact('blocks'));  
    }
     public function destroy(Request $request, $id)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'delete_maintenance')))) {
            return redirect()->back()->with('error', 'Unauthorized access to delete repairs.');
        }

        $repair = Repairs::findOrFail($id);
        $repair->delete();

        return redirect()->route('maintenance.index')->with('success', 'Repair record deleted successfully.');
    }
    
  public function loadEdit(Request $request, $repair_id)
{
    $user = Session::get('user');
    $permissions = Session::get('permissions');
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_maintenance')))) {
        return redirect()->back()->with('error', 'Unauthorized access to edit repairs.');
    }
    $repair = Repairs::with('block')->findOrFail($repair_id);
    return view('layouts.repairs.edit', compact('repair'));  
}

public function update(Request $request, $repair_id)
{
    $user = Session::get('user');
    $permissions = Session::get('permissions');
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_maintenance')))) {
        return redirect()->back()->with('error', 'Unauthorized access to update repairs.');
    }
    // Optional: validate incoming data
    $request->validate([
        'block_id' => 'required|integer|exists:block_models,id',
        'apartment_id' => 'required|integer|exists:apartment_identities,id',
        'unit_number' => 'required|string|max:255',
        'received_date' => 'nullable|date',
        'progress' => 'nullable|string|max:255',
        'status' => 'nullable|string|max:255',
        'repair_type' => 'nullable|string|max:255',
        'deadline_timeframe' => 'nullable|string|max:255',
        'issue' => 'nullable|string|max:255',
        'appointment_timeframe' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'action_timeline' => 'nullable|string|max:255',
        'assigned_to' => 'nullable|string|max:255',
        'ref' => 'nullable|string|max:255',
        'due_date' => 'nullable|date',
        'appointment' => 'nullable|date',
        'completion_date' => 'nullable|date',
    ]);

    // Find the repair record
    $repair = Repair::findOrFail($repair_id);

    // Update fields
    $repair->update([
        'block_id' => $request->block_id,
        'apartment_id' => $request->apartment_id,
        'unit_number' => $request->unit_number,
        'received_date' => $request->received_date,
        'progress' => trim($request->progress),
        'status' => trim($request->status),
        'repair_type' => trim($request->repair_type),
        'deadline_timeframe' => trim($request->deadline_timeframe),
        'issue' => trim($request->issue),
        'appointment_timeframe' => trim($request->appointment_timeframe),
        'description' => trim($request->description),
        'action_timeline' => trim($request->action_timeline),
        'assigned_to' => trim($request->assigned_to),
        'ref' => trim($request->ref),
        'due_date' => $request->due_date,
        'appointment' => $request->appointment,
        'completion_date' => $request->completion_date,
    ]);

    // Redirect back with success
    return redirect()->route('maintenance.index')->with('success', 'Repair updated successfully.');
}
public function store(Request $request)
{
    $user = Session::get('user');
    $permissions = Session::get('permissions');
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_maintenance')))) {
        return redirect()->back()->with('error', 'Unauthorized access to create repairs.');
    }   
    // Validate the incoming request
    $request->validate([
        'block_id' => 'required|integer|exists:block_models,id',
        'apartment_id' => 'required|integer|exists:apartment_identities,id',
        'unit_number' => 'required|string|max:255',
        'received_date' => 'nullable|date',
        'progress' => 'nullable|string|max:255',
        'status' => 'nullable|string|max:255',
        'repair_type' => 'nullable|string|max:255',
        'deadline_timeframe' => 'nullable|string|max:255',
        'issue' => 'nullable|string|max:255',
        'appointment_timeframe' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'action_timeline' => 'nullable|string|max:255',
        'assigned_to' => 'nullable|string|max:255',
        'ref' => 'nullable|string|max:255',
        'due_date' => 'nullable|date',
        'appointment' => 'nullable|date',
        'completion_date' => 'nullable|date',
    ]);

    // Create a new Repairs record
    $repair = Repairs::create([
        'block_id' => $request->block_id,
        'apartment_id' => $request->apartment_id,
        'unit_number' => $request->unit_number,
        'received_date' => $request->received_date,
        'progress' => trim($request->progress),
        'status' => trim($request->status),
        'repair_type' => trim($request->repair_type),
        'deadline_timeframe' => trim($request->deadline_timeframe),
        'issue' => trim($request->issue),
        'appointment_timeframe' => trim($request->appointment_timeframe),
        'description' => trim($request->description),
        'action_timeline' => trim($request->action_timeline),
        'assigned_to' => trim($request->assigned_to),
        'ref' => trim($request->ref),
        'due_date' => $request->due_date,
        'appointment' => $request->appointment,
        'completion_date' => $request->completion_date,
    ]);

    // Redirect or return response
    return redirect()->route('maintenance.index')->with('success', 'Repair record created successfully.');
}


}
