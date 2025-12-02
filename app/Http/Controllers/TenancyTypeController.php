<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EstateOwner;
use App\Models\TenancyTypeModel as TenancyType;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

class TenancyTypeController extends Controller
{
    public function index()
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');

        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_tenancy')))) {
            return redirect()->back()->with('error', 'Unauthorized access to tenancy types.');
        }

        $tenancyTypes = TenancyType::paginate(15);
        return view('layouts.tenancy.index', compact('tenancyTypes'));
    }

    public function create()
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');

        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_tenancy')))) {
            return redirect()->back()->with('error', 'Unauthorized access to tenancy.');
        }

        return view('layouts.tenancy.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenancy_type'   => 'required|array',
            'tenancy_type.*' => 'required|string|max:255'
        ]);

        $user = Session::get('user');
        $permissions = Session::get('permissions');

        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_tenancy')))) {
            return redirect()->back()->with('error', 'Unauthorized access to tenancy types.');
        }

        foreach ($validated['tenancy_type'] as $type) {
            TenancyType::create(['name' => $type]);
        }

        // Clear and regenerate cache
        Cache::forget('tenancy_types');
        Cache::remember('tenancy_types', 3600, fn () => TenancyType::all());

        return redirect()->route('tenancy.index')->with('success', 'Tenancy types added successfully!');
    }

    public function show(TenancyType $tenancyType)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');

        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_tenancy')))) {
            return redirect()->back()->with('error', 'Unauthorized access to tenancy types.');
        }

        return view('layouts.tenancy.create', compact('tenancyType'));
    }

    public function edit(TenancyType $tenancyType)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');

        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_tenancy')))) {
            return redirect()->back()->with('error', 'Unauthorized access to tenancy types.');
        }

        return view('layouts.tenancy.edit', compact('tenancyType'));
    }

    public function update(Request $request, TenancyType $tenancyType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = Session::get('user');
        $permissions = Session::get('permissions');

        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_tenancy')))) {
            return redirect()->back()->with('error', 'Unauthorized access to tenancy types.');
        }

        $tenancyType->update($validated);

        // Clear and regenerate cache
        Cache::forget('tenancy_types');
        Cache::remember('tenancy_types', 3600, fn () => TenancyType::all());

        return redirect()->route('tenancy.index')->with('success', 'Tenancy type updated successfully!');
    }

    public function destroy(TenancyType $tenancyType)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');

        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'delete_tenancy')))) {
            return redirect()->back()->with('error', 'Unauthorized access to tenancy methods.');
        }

        $tenancyType->delete();

        // Clear and regenerate cache
        Cache::forget('tenancy_types');
        Cache::remember('tenancy_types', 3600, fn () => TenancyType::all());

        return redirect()->route('tenancy.index')->with('success', 'Tenancy type deleted successfully!');
    }
}
