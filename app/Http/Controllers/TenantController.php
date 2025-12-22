<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\TenantModel as Tenant;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class TenantController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the form for creating a tenant.
     */
    public function loadCreateForm()
    {

        $user = Auth::user();
        $permissions = session('permissions');
        
        if (!$user->is_system_admin==='1' || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_tenant')))) {
            return redirect()->back()->with('error', 'Unauthorized access to tenancy types.');
        }
        return view('layouts.tenants.addForm');
    }


    public function create(Request $request)
{
    $user = Auth::user();
    $permissions = session('permissions');
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_tenant')))) {
        return redirect()->back()->with('error', 'Unauthorized access to create tenants.');
    }

    $rules = [
        // Step 1: Occupant Basic Details
        'full_name'     => 'required|string',
        'date_of_birth'  => 'required|date|before:+18 years',
        'gender'         => 'required|in:male,female,other',

        // Step 2: Identification Details
        'nationality'          => 'required|string',
        'state'                => 'required|string',
        'address'              => 'required|string',
        'id_method'            => 'required|in:driver_licence,nin,nis,passport',
        'identification_image' => 'required|image|mimes:png,jpeg,jpg|max:1024',
        'passport_photograph'  => 'required|image|mimes:png,jpeg,jpg|max:1024',

        // Step 3: Contact Details
        'mobile_number'    => 'required|regex:/^[0-9]{10,14}$/|unique:tenants,mobile_number',
        'home_number'      => 'required|regex:/^[0-9]{10,14}$/|unique:tenants,home_number',
        'occupant_email'   => 'nullable|email|unique:tenants,occupant_email',
        'emergency_contact'=> 'required|regex:/^[0-9]{10,14}$/',
        'emergency_email'  => 'nullable|email|unique:tenants,emergency_email',

        // Step 4: Guarantor Details//changed to next of kin
        'guarantor_full_name' => 'required|string|max:255',
        'guarantor_address'   => 'nullable|string',
        'guarantor_phone'     => 'nullable|regex:/^[0-9]{10,14}$/|unique:tenants,guarantor_phone',
        // 'guarantor_passport'  => 'required|image|mimes:png,jpeg,jpg|max:1024',
        'guarantor_email'=>'nullable|email',
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        // Do something if validation fails
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    // If validation passes, get validated data
    $validated = $validator->validated();

    // Create new tenant (ensure your Tenant model has the appropriate $fillable)
    $tenant = new Tenant($validated);

    // Handle file uploads on the "private" disk
    $tenant->identification_image = $request->file('identification_image')->store('identification_images', 'private');
    $tenant->passport_photograph  = $request->file('passport_photograph')->store('passport_photographs', 'private');
   // $tenant->guarantor_passport   = $request->file('guarantor_passport')->store('guarantor_passports', 'private');

    $tenant->save();

    // Clear the cache here when a new tenant is created
    Cache::forget('tenants_list');

    return redirect()->route('occupant.create.form')->with('success', 'Tenant created successfully.');
}

    public function index()
    {
        $user = Auth::user();
        $permissions = session('permissions');  
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_tenant')))) {
            return redirect()->back()->with('error', 'Unauthorized access to tenancy types.');
        }

        $tenants = Tenant::paginate(20);
    
        return view('layouts.tenants.index', compact('tenants'));
    }

    /**
     * Update the specified tenant in storage.
     */
    public function update(Request $request)
    {
    
        $user = Auth::user();
        $permissions = session('permissions');  
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_tenant')))) {
            return redirect()->back()->with('error', 'Unauthorized access to update tenants.');
        }
         $tenant = Tenant::findOrFail(strip_tags($request->tenant_id));
        $validated = $request->validate([
            // Step 1: Occupant Basic Details
            'full_name'     => 'required|string',
            'date_of_birth'  => 'nullable|date|before:+18 years',
            'gender'         => 'required|in:male,female,other',

            // Step 2: Identification Details
            'nationality'          => 'required|string',
            'state'                => 'required|string',
            'address'              => 'required|string',
            'id_method'            => 'required|in:driver_licence,nin,nis,passport',
            'identification_image' => 'nullable|image|mimes:png,jpeg,jpg|max:1024',
            'passport_photograph'  => 'nullable|image|mimes:png,jpeg,jpg|max:1024',

            // Step 3: Contact Details
            'mobile_number'    => 'required|regex:/^[0-9]{10,14}$/',
            'home_number'      => 'required|regex:/^[0-9]{10,14}$/',
            'occupant_email'   => 'nullable|email|unique:tenants,occupant_email,' . $tenant->id,
            'emergency_contact'=> 'required|regex:/^[0-9]{10,14}$/',
            'emergency_email'  => 'nullable|email',

            // Step 4: Guarantor Details
            'guarantor_full_name' => 'required|string|max:255',
            'guarantor_address'   => 'nullable|string',
            'guarantor_phone'     => 'required|regex:/^[0-9]{10,14}$/',
            'guarantor_passport'  => 'nullable|image|mimes:png,jpeg,jpg|max:1024',
            'guarantor_email'=>'nullable|email',
            'tenant_id'         =>'required|numeric'
        ]);
       

        // Mass assign validated fields
        $tenant->fill($validated);

        // Handle updates for file uploads if a new file is provided:
        if ($request->hasFile('identification_image')) {
            if ($tenant->identification_image && Storage::disk('private')->exists($tenant->identification_image)) {
                Storage::disk('private')->delete($tenant->identification_image);
            }
            $tenant->identification_image = $request->file('identification_image')->store('identification_images', 'private');
        }

        if ($request->hasFile('passport_photograph')) {
            if ($tenant->passport_photograph && Storage::disk('private')->exists($tenant->passport_photograph)) {
                Storage::disk('private')->delete($tenant->passport_photograph);
            }
            $tenant->passport_photograph = $request->file('passport_photograph')->store('passport_photographs', 'private');
        }

        if ($request->hasFile('guarantor_passport')) {
            if ($tenant->guarantor_passport && Storage::disk('private')->exists($tenant->guarantor_passport)) {
                Storage::disk('private')->delete($tenant->guarantor_passport);
            }
            $tenant->guarantor_passport = $request->file('guarantor_passport')->store('guarantor_passports', 'private');
        }

        $tenant->save();
        Cache::forget('tenants_list');

        return redirect()->route('occupant.index')->with('success', 'Tenant updated successfully.');
    }

    /**
     * Remove the specified tenant from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $permissions = session('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'delete_tenant')))) {
            return redirect()->back()->with('error', 'Unauthorized access to delete tenants.');
        }

        $tenant = Tenant::findOrFail($id);

        // Remove associated files using Storage facade
        if ($tenant->identification_image && Storage::disk('private')->exists($tenant->identification_image)) {
            Storage::disk('private')->delete($tenant->identification_image);
        }

        if ($tenant->passport_photograph && Storage::disk('private')->exists($tenant->passport_photograph)) {
            Storage::disk('private')->delete($tenant->passport_photograph);
        }

        if ($tenant->guarantor_passport && Storage::disk('private')->exists($tenant->guarantor_passport)) {
            Storage::disk('private')->delete($tenant->guarantor_passport);
        }
Cache::forget('tenants_list');
        $tenant->delete();

        return redirect()->route('occupant.index')->with('success', 'Tenant deleted successfully.');
    }

   
    public function getImage($filename)
{
    // Ensure the user is authenticated
    $user = Auth::user();
    $permissions = session('permissions');
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_tenant')))) {
        return redirect()->back()->with('error', 'Unauthorized access to view tenant images.');
    }
    if (!Auth::check()) {
        abort(403, 'Unauthorized access.');
    }

    // The $filename parameter will include the subdirectory (e.g., 'identification_images/jFloxE8ldhNiZohg7NIMk4WTBHhZ3N8j59F7Jw5r.png')
    $path = storage_path('app/private/' . $filename);


    if (!file_exists($path)) {
        abort(404, 'File not found: ' . $path);
    }

    $mimeType = mime_content_type($path);
    $validMimeTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];

    if (!in_array($mimeType, $validMimeTypes)) {
        abort(403, 'Invalid file type.');
    }

    return response()->file($path, [
        'Content-Type' => $mimeType,
        'Content-Disposition' => 'inline',
    ]);
}
   public function loadUpdatePage(Request $request)
{
    $user = Auth::user();
    $permissions = session('permissions');
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_tenant')))) {
        return redirect()->back()->with('error', 'Unauthorized access to update tenants.');
    }
    // Validate the occupant_id from the request
    $validator = Validator::make($request->all(), [
        'occupant_id' => 'required|integer|exists:tenants,id'
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    // Get the validated occupant_id
    $id = $request->input('occupant_id');

    // Fetch the tenant using findOrFail for a single record
    $tenant = Tenant::findOrFail($id);
    // Pass the tenant to the view
    return view('layouts.tenants.edit', compact('tenant'));
}
    
}
