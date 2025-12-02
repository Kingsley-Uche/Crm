<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlockModel;
use App\Models\Shelters;
use App\Models\Repairs;
use App\Models\VoidsModel;
use App\Models\ParkCategory;
use App\Models\ParkModel;
use App\Models\ParkTaxes;
use App\Models\ParkPermits;
use App\Models\permitTaxes;
use App\Models\TenantModel as Tenant;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\PermitCreatedMail;
use App\Models\ParkTrack;


class ParkController extends Controller
{
    // --- ParkModel CRUD ---

    /**
     * Display a listing of parks.
     *
     * @return \Illuminate\View\View
     */
    public function indexParks()
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_park')))) {
            return redirect()->back()->with('error', 'Unauthorized access to parks.');
        }
        try {
            $parks = ParkModel::with('category:id,name')->get();
                    return view('layouts.park.location.index', compact('parks'));
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to fetch parks: ' . $e->getMessage());
            return redirect()->route('layouts.park.location.create');
        }
    }

    /**
     * Show the form for creating a new park.
     *
     * @return \Illuminate\View\View
     */
 public function createPark()
{
      $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_park')))) {
            return redirect()->back()->with('error', 'Unauthorized access to parks.');
        }
    $categories = ParkCategory::all(); 

    return view('layouts.park.location.create', compact('categories'));
}


    /**
     * Store a new park.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
  public function storePark(Request $request)
    {
          $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_park')))) {
            return redirect()->back()->with('error', 'Unauthorized access to parks.');
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'category_id' => 'required|exists:park_categories,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();
            ParkModel::create([
                'name' => $request->name,
                'location' => $request->location,
                'address' => $request->address,
                'category_id' => $request->category_id,
            ]);
            DB::commit();

            Session::flash('success', 'Park created successfully');
            return redirect()->route('park.models.create');
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', 'Failed to create park: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

public function editPark(Request $request, $park_id)
{
      $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_park')))) {
            return redirect()->back()->with('error', 'Unauthorized access to parks.');
        }
    $park = ParkModel::with('category:id,name')->findOrFail($park_id);
    $categories = ParkCategory::all();
    return view('layouts.park.location.edit', compact(['park', 'categories']));
}

    /**
     * Update an existing park.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePark(Request $request, $id)
    {
        
          $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_park')))) {
            return redirect()->back()->with('error', 'Unauthorized access to parks.');
        }
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'location' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:500',
            'category_id' =>'sometimes|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
       

        try {
            $park = ParkModel::findOrFail($id);
            DB::beginTransaction();
            $park->update($request->only(['name', 'location', 'address','category_id']));
            DB::commit();

            Session::flash('success', 'Park updated successfully');
            return redirect()->route('park.models.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', 'Failed to update park: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Delete a park.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyPark($id)
    {
        
          $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'delete_park')))) {
            return redirect()->back()->with('error', 'Unauthorized access to parks.');
        }
        try {
            $park = ParkModel::findOrFail($id);
            DB::beginTransaction();
            $park->delete();
            DB::commit();

            Session::flash('success', 'Park deleted successfully');
            return redirect()->route('park_models.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', 'Failed to delete park: ' . $e->getMessage());
            return redirect()->route('park_models.index');
        }
    }

    // --- ParkCategory CRUD ---

    /**
     * Display a listing of park categories.
     *
     * @return \Illuminate\View\View
     */
    public function indexCategories()
    {
          $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_park')))) {
            return redirect()->back()->with('error', 'Unauthorized access to parks.');
        }
         
     
             $categories = ParkCategory::all();
           
            return view('layouts.park.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new park category.
     *
     * @return \Illuminate\View\View
     */
    public function createCategory()
    {
          $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_park')))) {
            return redirect()->back()->with('error', 'Unauthorized access to parks.');
        }
    
        return view('layouts.park.categories.create');
    }
    
    public function editCategory($id)
{
    $park_category = ParkCategory::findOrFail($id);

    return view('layouts.park.categories.edit', compact('park_category'));
}

    /**
     * Store a new park category.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeCategory(Request $request)
    {
          $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_park')))) {
            return redirect()->back()->with('error', 'Unauthorized access to parks.');
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:park_categories,name',
            'features' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();
            ParkCategory::create([
                'name' => $request->name,
                'features' => $request->features,
            ]);
            DB::commit();

            Session::flash('success', 'Category created successfully');
            return redirect()->route('park.categories.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', 'Failed to create category: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Update an existing park category.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateCategory(Request $request, $id)
    {
          $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_park')))) {
            return redirect()->back()->with('error', 'Unauthorized access to parks.');
        }
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255|unique:park_categories,name,' . $id,
            'features' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $category = ParkCategory::findOrFail($id);
            DB::beginTransaction();
            $category->update($request->only(['name', 'features']));
            DB::commit();

            Session::flash('success', 'Category updated successfully');
            return redirect()->route('park.categories.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', 'Failed to update category: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Delete a park category.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyCategory($id)
    {
          $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'delete_park')))) {
            return redirect()->back()->with('error', 'Unauthorized access to parks.');
        }
        try {
            $category = ParkCategory::findOrFail($id);
            DB::beginTransaction();
            $category->delete();
            DB::commit();

            Session::flash('success', 'Category deleted successfully');
            return redirect()->route('park.categories.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', 'Failed to delete category: ' . $e->getMessage());
            return redirect()->route('park.categories.index');
        }
    }

    // --- ParkTaxes CRUD ---

    /**
     * Display a listing of park taxes.
     *
     * @return \Illuminate\View\View
     */
    public function indexTaxes()
    {
          $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_park')))) {
            return redirect()->back()->with('error', 'Unauthorized access to parks.');
        }
        try {
            $taxes = ParkTaxes::all();
            return view('layouts.park.taxes.index', compact('taxes'));
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to fetch taxes: ' . $e->getMessage());
            return redirect()->route('layouts.park.taxes.index');
        }
    }

    /**
     * Show the form for creating a new park tax.
     *
     * @return \Illuminate\View\View
     */
    public function createTax()
    {
          $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_park')))) {
            return redirect()->back()->with('error', 'Unauthorized access to parks.');
        }
        return view('layouts.park.taxes.create');
    }

    /**
     * Store a new park tax.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeTax(Request $request)
    {
          $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_park')))) {
            return redirect()->back()->with('error', 'Unauthorized access to parks.');
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:park_taxes,name',
            'rate' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();
            ParkTaxes::create([
                'name' => $request->name,
                'rate' => $request->rate,
                'description' => $request->description,
            ]);
            DB::commit();

            Session::flash('success', 'Tax created successfully');
            return redirect()->route('park.taxes.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', 'Failed to create tax: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Update an existing park tax.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateTax(Request $request, $id)
    {
          $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_park')))) {
            return redirect()->back()->with('error', 'Unauthorized access to parks.');
        }
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255|unique:park_taxes,name,' . $id,
            'rate' => 'sometimes|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $tax = ParkTaxes::findOrFail($id);
            DB::beginTransaction();
            $tax->update($request->only(['name', 'rate', 'description']));
            DB::commit();

            Session::flash('success', 'Tax updated successfully');
            return redirect()->route('park.taxes.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', 'Failed to update tax: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Delete a park tax.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyTax($id)
    {
          $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'delete_park')))) {
            return redirect()->back()->with('error', 'Unauthorized access to parks.');
        }
        try {
            $tax = ParkTaxes::findOrFail($id);
            DB::beginTransaction();
            $tax->delete();
            DB::commit();

            Session::flash('success', 'Tax deleted successfully');
            return redirect()->route('park.taxes.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', 'Failed to delete tax: ' . $e->getMessage());
            return redirect()->route('park.taxes.index');
        }
    }

    // --- ParkPermit CRUD ---

    /**
     * Display a listing of parking permits.
     *
     * @return \Illuminate\View\View
     */
    public function indexPermits()
    {
        try {
            $permits = ParkPermits::with(['parkCategory', 'park'])->get();
            
            return view('layouts.park.permits.index', compact('permits'));
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to fetch permits: ' . $e->getMessage());
            return redirect()->route('park.permits.index');
        }
    }

    /**
     * Show the form for creating a new parking permit.
     *
     * @return \Illuminate\View\View
     */
    public function createPermit()
    {
        $categories = ParkCategory::all();// use select dropdown
        $parks = ParkModel::all();//use select drop down
        $taxes = ParkTaxes::all();//check use switches
        return view('layouts.park.permits.create', compact('categories', 'parks', 'taxes'));
    }

    /**
     * Store a new parking permit.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */

     public function storePermit(Request $request)
    {
        
          $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_park')))) {
            return redirect()->back()->with('error', 'Unauthorized access to parks.');
        }

        $validated = $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'email'        => 'required|email|max:150',
            'phone'        => 'required|string|max:20',
            'permit_name'  => 'required|string|max:255',
            'park_id'      => 'required|exists:park_models,id',
            'category_id'  => 'required|exists:park_categories,id',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'fee'          => 'required|numeric|min:0',
            'taxes'        => 'nullable|array',
            'taxes.*'      => 'exists:park_taxes,id',
        ]);
        $data =['first_name'=>$validated['first_name'], 'last_name'=>$validated['last_name'], 'start_date'=>$validated['start_date'], 'end_date'=>$validated['end_date']];
$code = ParkPermits::GenCode($data);
$pass_code =ParkPermits::generatePasscode(); 
        DB::beginTransaction();

        try {
            $permit = ParkPermits::create([
                'fname'   => $validated['first_name'],
                'lname'    => $validated['last_name'],
                'email'        => $validated['email'],
                'phone'        => $validated['phone'],
                'permit_name'  => $validated['permit_name'],
                'park_model_id'      => $validated['park_id'],
                'park_category_id'  => $validated['category_id'],
                'start_time'   => $validated['start_date'],
                'end_time'     => $validated['end_date'],
                'fee'          => $validated['fee'],
                'uniqueId'=>$code,
                'pass_code'=>$pass_code,
                
            ]);
            if (isset($validated['taxes'])) {
               $taxes = $validated['taxes'];
                 $this->createTaxPermit($taxes, $permit->id);
            }
            $permit->caption ='created';
           $permit->load(['park:id,name']);
           //send email of permit number to user
            Mail::to($permit->email)->send(new PermitCreatedMail($permit));

            DB::commit();

            return redirect()->route('park.permits.index')->with('success', 'Permit created successfully and emailed to user');
        } catch (\Exception $e) {
        
            DB::rollBack();
            return back()->withErrors('An error occurred while saving the permit: ' . $e->getMessage());
        }
    }
    
    /**
 * Show the form for editing an existing parking permit.
 *
 * @param int $id
 * @return \Illuminate\View\View
 */
public function editPermit($id)
{
      $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_park')))) {
            return redirect()->back()->with('error', 'Unauthorized access to parks.');
        }
    try {
        $permit = ParkPermits::with('taxes')->findOrFail($id);
        $categories = ParkCategory::all();
        $parks = ParkModel::all();
        $taxes = ParkTaxes::all();
        return view('layouts.park.permits.edit', compact('permit', 'categories', 'parks', 'taxes'));
    } catch (\Exception $e) {
        Session::flash('error', 'Failed to fetch permit: ' . $e->getMessage());
        return redirect()->route('park.permits.index');
    }
}

   
   public function updatePermit(Request $request, $id)
{
      $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_park')))) {
            return redirect()->back()->with('error', 'Unauthorized access to parks.');
        }
    $validated = $request->validate([
        'first_name'   => 'sometimes|string|max:100',
        'last_name'    => 'sometimes|string|max:100',
        'email'        => 'sometimes|email|max:150',
        'phone'        => 'sometimes|string|max:20',
        'permit_name'  => 'sometimes|string|max:255',
        'park_id'      => 'sometimes|exists:park_models,id',
        'category_id'  => 'sometimes|exists:park_categories,id',
        'start_date'   => 'sometimes|date',
        'end_date'     => 'sometimes|date|after_or_equal:start_date',
        'fee'          => 'sometimes|numeric|min:0',
        'taxes'        => 'nullable|array',
        'taxes.*'      => 'exists:permit_taxes,id',
    ]);

   try {
        $permit = ParkPermits::findOrFail($id);

        DB::beginTransaction();

        // Update permit with only validated data
        $permit->update($validated);
        $permit->caption ='modified';

        // Sync taxes if provided
        if (isset($validated['taxes'])) {
            $permit->taxes()->sync($validated['taxes']);
        } else {
            $permit->taxes()->sync([]); // remove all taxes if none provided
        }
          Mail::to($permit->email)->send(new PermitCreatedMail($permit));

        DB::commit();

        Session::flash('success', 'Permit updated successfully');
        return redirect()->route('park.permits.index');
   } catch (\Exception $e) {
        DB::rollBack();
        Session::flash('error', 'Failed to update permit: ' . $e->getMessage());
        return redirect()->back()->withInput();
    }
}

public function destroyPermit($id)
{
      $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'delete_park')))) {
            return redirect()->back()->with('error', 'Unauthorized access to parks.');
        }
    try {
        $permit = ParkPermits::findOrFail($id);

        DB::beginTransaction();

        // Detach related taxes before deleting (optional but safe)
        $permit->taxes()->detach();

        $permit->delete();

        DB::commit();

        Session::flash('success', 'Permit deleted successfully');
        return redirect()->route('park.permits.index');
    } catch (\Exception $e) {
        DB::rollBack();
        Session::flash('error', 'Failed to delete permit: ' . $e->getMessage());
        return redirect()->route('park.permits.index');
    }
    
}
private function createTaxPermit($data, $permit_id)
{
    
    foreach ($data as $tax_id) {
        permitTaxes::create([
            'tax_id' => $tax_id,
            'permit_id' => $permit_id
        ]);
    }
}
public function capture(){
   
    return view('layouts.parking.bound');
}



public function inbound(Request $request)
{
    $user = Session::get('user');

    // Validate request
    $validated = $request->validate([
        'passcode' => 'required|string|max:6',
    ]);

    // Find the permit ID based on passcode
    $permit_id = ParkPermits::where('pass_code', $validated['passcode'])->value('id');

    if (!$permit_id) {
        return response()->json(['message' => 'Invalid passcode'], 404);
    }

    try {
        // Create inbound entry only if no open (unclosed) record exists
        $alreadyInbound = ParkTrack::where('permit_id', $permit_id)
            ->whereNull('outbound_time')
            ->exists();

        if ($alreadyInbound) {
            return response()->json(['message' => 'This vehicle is already inbound and not yet outbound.'], 409);
        }

        ParkTrack::create([
            'permit_id' => $permit_id,
            'inbound_admin_id' => $user->id ?? null,
            'inbound_time' => Carbon::now(),
        ]);

        return response()->json(['message' => 'Inbound recorded successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
    }
}

public function outbound(Request $request)
{
    $user = Session::get('user');

    // Validate request
    $validated = $request->validate([
        'passcode' => 'required|string|max:6',
    ]);

    // Find the permit ID based on passcode
    $permit_id = ParkPermits::where('pass_code', $validated['passcode'])->value('id');

    if (!$permit_id) {
        return response()->json(['message' => 'Invalid passcode'], 404);
    }

    try {
        // Find the latest unclosed ParkTrack record
        $track = ParkTrack::where('permit_id', $permit_id)
            ->whereNull('outbound_time')
            ->latest('inbound_time')
            ->first();

        if (!$track) {
            return response()->json(['message' => 'No matching inbound record found for this passcode.'], 404);
        }

        $track->update([
            'outbound_admin_id' => $user->id ?? null,
            'outbound_time' => Carbon::now(),
        ]);

        return response()->json(['message' => 'Outbound recorded successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
    }
}
    public function load_status(){
        //use from and to time 
         return view('layouts.parking.load_status');
        
        
    }
 public function status(Request $request)
{
    $validated = $request->validate([
        'start_date' => 'required|date',
        'end_date'   => 'required|date|after_or_equal:start_date',
    ]);

    // Parse dates and cover full days
    $start = Carbon::parse($validated['start_date'])->startOfDay(); // 00:00:00
    $end = Carbon::parse($validated['end_date'])->endOfDay();       // 23:59:59

    $data = ParkTrack::with([
        'parkPermit:id,fname,lname,uniqueid',
        'inboundAdmin:id,fName,lName',
        'outboundAdmin:id,fName,lName'
    ])
    ->whereBetween('created_at', [$start, $end])
    ->get();
    

    return view('layouts.parking.partials.park_status_table', compact('data'));
}

}
?>