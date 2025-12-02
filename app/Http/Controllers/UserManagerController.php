<?php

namespace App\Http\Controllers;

use App\Models\AdminModel as User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Models\RolesModel;
use App\Models\PermissionsModel; // Assuming you have a PermissionsModel for user permissions
use Illuminate\Support\Facades\Session;
class UserManagerController extends Controller
{
  


public function store(Request $request)
{
      $user = auth()->user();
        $permissions = session('permissions');
       if (!$user || (!$user->is_system_admin==='1')){
            return redirect()->back()->with('error', 'Unauthorized access to users module.');
        }
    $request->validate([
        'fname' => 'required|string|max:255',
        'lname' => 'required|string|max:255',
        'email' => 'required|email|unique:admin_models,email',
        'role_id' => 'required|exists:roles_models,id', // Assuming you have a roles table
    ]);

    // Generate random password (8 chars with letters and symbols)
    $password = $this->generateRandomPassword(8);
     $receiver = new User();

    // Create user with hashed password
   
    $receiver->fname = $request->fname;
    $receiver->lname = $request->lname;
    $receiver->email = $request->email;
    $receiver->password = Hash::make($password);
    $receiver->role_id = $request->role_id;
    $receiver->created_by_admin_id = Auth::id();
    $receiver->save();

    // Send email with password
    Mail::to($receiver->email)->send(new \App\Mail\UserPasswordMail($receiver, $password));

    return redirect()->route('access.users.index')->with('success', 'User created and password emailed successfully.');
}

/**
 * Generate random password with letters, numbers and symbols
 */
protected function generateRandomPassword($length = 8)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+[]{}|;:,.<>?';
    return substr(str_shuffle(str_repeat($chars, $length)), 0, $length);
}
  // List all users
    public function index()
    {
         $user = auth()->user();
        $permissions = session('permissions');
         if (!$user || (!$user->is_system_admin==='1')){
            return redirect()->back()->with('error', 'Unauthorized access to user module.');
        }
        $admins = User::where('is_system_admin', '!=', '1')->get(); // pagination example
        return view('layouts.access.users.index', compact('admins'));
    }

    // Show form to create a new user
    public function create()
    {
         $user = auth()->user();
        $permissions = session('permissions');
         if (!$user || (!$user->is_system_admin==='1')) {
            return redirect()->back()->with('error', 'Unauthorized access to roles.');
        }
        $roles = RolesModel::all(); // Assuming you have a RolesModel for user roles
        return view('layouts.access.users.create', compact('roles'));
    }

    // Show user details
    public function show(request $request,$id)
    {
         $user = auth()->user();
        $permissions = session('permissions');
       if (!$user || (!$user->is_system_admin==='1')) {
            return redirect()->back()->with('error', 'Unauthorized access to user module.');
        }
        $admin = User::findOrFail($id);
        if ($user->is_admin_id ==='1') {
            return redirect()->route('layouts.access.users.index')->with('error', 'User not found or does not have a role assigned.');
        }
        
        // Check if the user is trying to view their own profile
        return view('access.users.show', compact('admin'));
    }

    // Show form to edit a user
    public function edit( $id)
    {
        $user = auth()->user();
        $permissions = session('permissions');
        if (!$user || (!$user->is_system_admin==='1')) {
            return redirect()->back()->with('error', 'Unauthorized access to user modules.');
        }  $admin = User::findOrFail($id);
        $roles = RolesModel::all(); // Assuming you have a RolesModel for user roles
        return view('layouts.access.users.update', compact('admin', 'roles'));
    }

    // Update user
    public function update(Request $request)

    {
 $user = auth()->user();
        $permissions = session('permissions');
        if (!$user || (!$user->is_system_admin==='1')){
            return redirect()->back()->with('error', 'Unauthorized access to roles.');
        }
    
        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:admin_models,email,' . $request->user_id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles_models,id', 
            'user_id' => 'required|exists:admin_models,id', // Ensure the user exists
        ]);

        $admin = User::findOrFail(trim($request->user_id));
        $admin->fname = $request->fname;
        $admin->lname = $request->lname;
        $admin->email = $request->email;
        $admin->role_id = $request->role_id; // Update role_id
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }
        $admin->update($admin->toArray());

        return redirect()->route('access.users.index')->with('success', 'User updated successfully.');
    }

    // Delete user
    public function destroy(User $user)
    {
         $user_logged = auth()->user();
        $permissions = session('permissions');
    
      if (!$user_logged || (!$user_logged->is_system_admin==='1')){
            return redirect()->back()->with('error', 'Unauthorized access to user module.');
        }
        $user->delete();
        return redirect()->route('access.users.index')->with('success', 'User deleted successfully.');
    }
    public function accessControl($user)
{

     
    if($user){
        if (isset($user->role_id) && $user->role_id !== null) {
        
        $user->system_admin = false;

        $role = RolesModel::select('id') // Only select needed fields from roles_models
            ->with(['permissions' => function ($query) {
                $query->select('id', 'slug'); // Only fetch required fields from permissions
            }])->find($user->role_id);

        $permissions = $role->permissions;
        return $permissions;
    }else if($user->is_system_admin===1){
        $permissions = PermissionsModel::select('id', 'slug')->get();
        return $permissions;
        
    }

   
    
    return $user->permissions;

    }
return $user->permissions;

}

}
