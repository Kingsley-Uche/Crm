<?php

namespace App\Http\Controllers;

use App\Models\LocationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\BranchModel;


class Location extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $permissions = session('permissions');  
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_property')))) {
            return redirect()->back()->with('error', 'Unauthorized access to locations.');
        }
       $locations = LocationModel::with('branch:id,name') ->select('id', 'name', 'branch_id')->get();
         
        return view('layouts.location.index', compact('locations'));
    }

    public function create()
    {
        $user = auth()->user();
        $permissions = session('permissions');  
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_property')))) {
            return redirect()->back()->with('error', 'Unauthorized access to create locations.');
        }
        $branches = BranchModel::all();
        return view('layouts.location.create', compact('branches'));
    }

    public function store(Request $request)
    {   
        $user = auth()->user();
        $permissions = session('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_property')))) {
            return redirect()->back()->with('error', 'Unauthorized access to create locations.');
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'branch_id' => 'nullable|exists:branch_models,id',
        ]);
        LocationModel::create([
            'name' => $request->name,
            'branch_id' => $request->branch_id,
        ]);

        return redirect()->route('locations.index')->with('success', 'Location created successfully.');
    }

    public function show($id)
    {
        $user = auth()->user();
        $permissions = session('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_property')))) {
            return redirect()->back()->with('error', 'Unauthorized access to locations.');
        }
        $location = LocationModel::findOrFail($id);
        return view('layouts.location.show', compact('location'));
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permissions = session('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_property')))) {
            return redirect()->back()->with('error', 'Unauthorized access to locations.');
        }
        $location = LocationModel::with('branch:id,name')->select('id', 'name', 'branch_id')->findOrFail($id);
         
        $branches = BranchModel::all();
        return view('layouts.location.edit', compact('location', 'branches'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $permissions = session('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_property')))) {
            return redirect()->back()->with('error', 'Unauthorized access to locations.');
        }

        $location = LocationModel::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'branch_id' => 'nullable|exists:branch_models,id',
        ]);

        $location->update([
            'name' => $request->name,
            'branch_id' => $request->branch_id,
        ]);

        return redirect()->route('locations.index')->with('success', 'Location updated successfully.');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permissions = session('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'delete_property')))) {
            return redirect()->back()->with('error', 'Unauthorized access to locations.');
        }

        $location = LocationModel::findOrFail($id);
        //check if location has blocks
        $location->delete();

        return redirect()->route('locations.index')->with('success', 'Location deleted successfully.');
    }
}
