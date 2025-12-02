<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EstateOwner;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
class EstateOwnerController extends Controller
{
    // Display a listing of estate owners
    public function index()
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_estate_owner')))) {
            return redirect()->back()->with('error', 'Unauthorized access to estate owners.');
        }
        $owners = EstateOwner::paginate(15);
        return view('layouts.estate_owner.index', compact('owners')); // Fixed view path
    }

    // Show the form for creating a new estate owner
    public function create()
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_estate_owner')))) {
            return redirect()->back()->with('error', 'Unauthorized access to create estate owners.');
        }
        return view('layouts.estate_owner.create'); // Fixed view path
    }

    // Store a new estate owner
    public function store(Request $request)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_estate_owner')))) {
            return redirect()->back()->with('error', 'Unauthorized access to create estate owners.');
        }
        // Validation rules
        $validator = Validator::make($request->all(), [
            'fName' => 'required|string|max:160',
            'lName' => 'required|string|max:160',
            'email' => 'required|email|unique:estate_owners,email',
            'phones' => 'required|string',
            'means_of_identification' => 'required|in:passport,nin,driver_licence,nis',
            'identification_image' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'address' => 'required|string|max:190',
            'next_of_kin' => 'required|string|max:160',
            'next_of_kin_phone' => 'required|string', // Added missing validation
            'bank_name' => 'required|string|max:160',
            'account_number' => 'required|numeric|digits:10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Store identification image
        $identificationImage = null;
        if ($request->hasFile('identification_image')) {
            $identificationImage = $request->file('identification_image')
                ->store('identification_images', 'public');
        }

        // Create new Estate Owner
        EstateOwner::create([
            'fName' => $request->fName,
            'lName' => $request->lName,
            'email' => $request->email,
            'phones' => $request->phones,
            'means_of_identification' => $request->means_of_identification,
            'identification_image' => $identificationImage,
            'address' => $request->address,
            'next_of_kin' => $request->next_of_kin,
            'next_of_kin_phone' => $request->next_of_kin_phone, // Added missing field
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
        ]);

        return redirect()->route('estate_owners.index')
            ->with('success', 'Estate Owner created successfully.');
    }

    // Show a specific estate owner
    public function show($id)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_estate_owner')))) {
            return redirect()->back()->with('error', 'Unauthorized access to estate owners.');
        }   
        $owner = EstateOwner::findOrFail($id);
        return view('estate_owners.show', compact('owner')); // Fixed view path
    }

    // Show the form for editing an estate owner
  public function edit($id)
{
    $user = Session::get('user');
    $permissions = Session::get('permissions');
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_estate_owner')))) {
        return redirect()->back()->with('error', 'Unauthorized access to edit estate owners.');
    }

    try {
        $owner = EstateOwner::findOrFail($id);
        return view('layouts.estate_owner.edit', compact('owner'));
    } catch (ModelNotFoundException $e) {
        return redirect()->route('estate_owners.index')
            ->with('error', 'Estate owner not found.');
    } catch (\Exception $e) {
        return redirect()->route('estate_owners.index')
            ->with('error', 'An unexpected error occurred while loading the estate owner.');
    }
}
    // Update an estate owner
    public function update(Request $request, $id)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions'); 
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_estate_owner')))) {
            return redirect()->back()->with('error', 'Unauthorized access to update estate owners.');
        }
        $owner = EstateOwner::findOrFail($id);

        // Validation rules
        $validator = Validator::make($request->all(), [
            'fName' => 'sometimes|required|string|max:255',
            'lName' => 'sometimes|required|string|max:255',
            'email' => "sometimes|required|email|unique:estate_owners,email,{$id}",
            'phones' => 'sometimes|required|string',
            'means_of_identification' => 'sometimes|required|in:passport,nin,driver_licence,nis',
            'identification_image' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048', // Changed to nullable
            'address' => 'sometimes|required|string|max:500',
            'next_of_kin' => 'sometimes|required|string|max:255',
            'next_of_kin_phone' => 'sometimes|required|string', // Added missing validation
            'bank_name' => 'sometimes|required|string|max:255',
            'account_number' => 'sometimes|required|numeric|digits:10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Handle identification image update
        $data = $request->all();
        if ($request->hasFile('identification_image')) {
            // Delete old image if it exists (optional)
            if ($owner->identification_image) {
                \Storage::disk('public')->delete($owner->identification_image);
            }
            $data['identification_image'] = $request->file('identification_image')
                ->store('identification_images', 'public');
        }

        // Update owner details
        $owner->update($data);

        return redirect()->route('estate_owners.index')
            ->with('success', 'Estate Owner updated successfully.');
    }

    // Delete an estate owner
    public function destroy($id)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'delete_estate_owner')))) {
            return redirect()->back()->with('error', 'Unauthorized access to delete estate owner.');
        }
        $owner = EstateOwner::findOrFail($id);

        // Delete associated image if it exists
        if ($owner->identification_image) {
            \Storage::disk('public')->delete($owner->identification_image);
        }
        
        $owner->delete();

        return redirect()->route('estate_owners.index')
            ->with('success', 'Estate Owner deleted successfully.');
    }
}