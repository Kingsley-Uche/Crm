<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RolesModel;
use App\Models\PermissionsModel;
use App\Models\Role_Permission; // Assuming you have a pivot model for role-permission relationships
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RolesController extends Controller
{
    // Show list of roles
    public function index()
    {
        $user = auth()->user();
          $user = auth()->user();
        $permissions = session('permissions');
        if (!$user || (!$user->is_system_admin==='1'))  {
            return redirect()->back()->with('error', 'Unauthorized access to roles.');
        }
        $roles = RolesModel::orderBy('created_at', 'desc')->get();
        return view('layouts.access.roles.index', compact('roles'));
    }

    // Show form to create new role
    public function create()
    {
            $user = auth()->user();
        $permissions = session('permissions');
        if (!$user || (!$user->is_system_admin==='1')) {
            return redirect()->back()->with('error', 'Unauthorized access to roles.');
        }
        $user = auth()->user();
        $permissions = session('permissions');
        $permissions = PermissionsModel::all();
        return view('layouts.access.roles.create', compact('permissions'));
    
    }

    // Store new role in database
    public function store(Request $request)
    {
         $user = auth()->user();
        $permissions = session('permissions');
        if (!$user || (!$user->is_system_admin==='1')) {
            return redirect()->back()->with('error', 'Unauthorized access to roles.');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',   
        ]);
        $valid_role = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]); 
        $role_id = RolesModel::create($valid_role)->id;
        foreach ($validated['permissions'] as $key=> $permission_id) {
            Role_Permission::Create(
                ['role_id' => $role_id, 'permission_id' => $permission_id]
            );

        
            
        }



        return redirect()->route('access.roles.index')
                         ->with('success', 'Role created successfully.');
    }

    // Show form to edit existing role
   public function edit($id)
    {
             $user = auth()->user();
        $permissions = session('permissions');
        if (!$user || (!$user->is_system_admin==='1')) {
            return redirect()->back()->with('error', 'Unauthorized access to roles.');
        }
        // Fetch the role, with its assigned permissions
        $role = RolesModel::with('permissions')->findOrFail($id);

        // Fetch all permissions for the checkbox list
        $permissions = PermissionsModel::all();

        // Pass both to the view
        return view('layouts.access.roles.update', compact('role', 'permissions'));
    }

    // Update existing role in database
    public function update(Request $request, $id)
{
        $user = auth()->user();
        $permissions = session('permissions');
        if (!$user || (!$user->is_system_admin==='1')) {
            return redirect()->back()->with('error', 'Unauthorized access to roles.');
        } 
    $role = RolesModel::findOrFail($id);

    // Validate role fields and permissions
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'permissions' => 'array',
    ]);

    // Update the role's basic info
    $role->update([
        'name' => $validated['name'],
        'description' => $validated['description'] ?? null,
    ]);

    // Sync role permissions
    // First, remove all existing permissions for this role
    Role_Permission::where('role_id', $role->id)->delete();

    // Then, add the new set
    if (!empty($validated['permissions'])) {
        foreach ($validated['permissions'] as $permission_id) {
            Role_Permission::create([
                'role_id' => $role->id,
                'permission_id' => $permission_id,
            ]);
        }
    }

    return redirect()->route('access.roles.index')
                     ->with('success', 'Role updated successfully.');
}

    // Delete role
    public function destroy($id)
{
         $user = auth()->user();
        $permissions = session('permissions');
        if (!$user || (!$user->is_system_admin==='1')) {
            return redirect()->back()->with('error', 'Unauthorized access to roles.');
        }
    $role = RolesModel::findOrFail($id);

    // Delete all permissions assigned to this role
    Role_Permission::where('role_id', $role->id)->delete();

    // Delete the role itself
    $role->delete();

    return redirect()->route('access.roles.index')
                     ->with('success', 'Role deleted successfully.');
}

}
