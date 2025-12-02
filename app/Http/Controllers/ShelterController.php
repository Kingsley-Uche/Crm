<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shelter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class ShelterController extends Controller
{
    // Create a new shelter
    public function createShelter(Request $request)
    {
         $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_property')))) {
            return redirect()->back()->with('error', 'Unauthorized access to parks.');
        }

        // Validate the input
        $validated = $request->validate([
            'name' => 'required|string|max:255', 
        ]);

        // If validation fails, Laravel automatically redirects back with errors

        // Create the shelter and save to the database
        Shelter::create([
            'name'        => $validated['name'],
            'created_by'  => Auth::id(),  // Assuming the admin is logged in and this is the admin_user_id
        ]);

        // Flash success message and redirect back
        return redirect()->back()->with('success', 'Shelter created successfully.');
    }

    // Get a list of all shelters
    public function index()
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_property')))) {
            return redirect()->back()->with('error', 'Unauthorized access to parks.');
        }
        $shelters = Shelter::all();  // Fetch all shelters
        return view('shelters.index', compact('shelters'));  // Pass shelters to the view
    }

    // Show a specific shelter
    public function show($id)
    {
         $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_property')))) {
            return redirect()->back()->with('error', 'Unauthorized access to properties.');
        }
        $shelter = Shelter::findOrFail($id);  // Find shelter by ID, 404 if not found
        return view('shelters.show', compact('shelter'));  // Pass shelter data to the view
    }

    // Edit shelter form
    public function edit($id)
    {
         $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_property')))) {
            return redirect()->back()->with('error', 'Unauthorized access to properties.');
        }     
        $shelter = Shelter::findOrFail($id);  // Fetch shelter by ID
        return view('shelters.edit', compact('shelter'));  // Load edit view with shelter data
    }

    // Update shelter
    public function update(Request $request, $id)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_property')))) {
            return redirect()->back()->with('error', 'Unauthorized access to properties.');
        }
        
        // Validate the input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Find and update the shelter
        $shelter = Shelter::findOrFail($id);
        $shelter->update([
            'name' => $validated['name'],
        ]);

        // Flash success message and redirect back
        return redirect()->route('shelters.index')->with('success', 'Shelter updated successfully.');
    }

    // Delete shelter
    public function destroy($id)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'delete_property')))) {
            return redirect()->back()->with('error', 'Unauthorized access to properties.');
        }
        $shelter = Shelter::findOrFail($id);  // Find shelter by ID
        $shelter->delete();  // Delete the shelter

        // Flash success message and redirect back
        return redirect()->route('shelters.index')->with('success', 'Shelter deleted successfully.');
    }
}
