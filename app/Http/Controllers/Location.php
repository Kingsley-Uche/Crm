<?php

namespace App\Http\Controllers;

use App\Models\LocationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class Location extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $permissions = session('permissions');  
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_property')))) {
            return redirect()->back()->with('error', 'Unauthorized access to locations.');
        }
        $locations = LocationModel::all();
        return view('layouts.location.index', compact('locations'));
    }

    public function create()
    {
        $user = auth()->user();
        $permissions = session('permissions');  
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_property')))) {
            return redirect()->back()->with('error', 'Unauthorized access to create locations.');
        }
        return view('layouts.location.create');
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
        ]);

        LocationModel::create([
            'name' => $request->name,
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
        $location = LocationModel::findOrFail($id);
        return view('layouts.location.edit', compact('location'));
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
        ]);

        $location->update([
            'name' => $request->name,
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
        $location->delete();

        return redirect()->route('locations.index')->with('success', 'Location deleted successfully.');
    }
}
