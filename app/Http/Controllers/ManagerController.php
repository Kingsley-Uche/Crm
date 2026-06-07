<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Manager;
use App\Models\ApartmentIdentity;
use App\Models\BranchModel;
use App\Models\LocationModel;
use App\Models\Shelter;
use App\Models\AdminModel as User;
use  App\Models\Shelter_Amenities;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;

class ManagerController extends Controller
{
    /**
     * Display all managers
     */
    public function index()
    {
        $managers = Manager::latest()->paginate(20);

        return view('layouts.managers.index', compact('managers'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('layouts.managers.create');
    }

    /**
     * Save manager
     */
   public function store(Request $request)
{
    $validated = $request->validate([
        'lName'    => 'required|string|max:255',
        'fName'    => 'required|string|max:255',
        'email'    => 'required|email|unique:managers,email|unique:users,email',
        'phone'    => 'nullable|string|max:20',
        'password' => 'required|string|min:8|confirmed',
    ]);

   DB::beginTransaction();

try {

    $plainPassword = $validated['password'];
    $hashedPassword = Hash::make($plainPassword);

    $user = User::create([
        'fname'      => $validated['fName'],
        'lname'      => $validated['lName'],
        'email'      => $validated['email'],
        'user_type'  => 2,
        'created_by' => Auth::id(),
        'password'   => $hashedPassword,
    ]);

    $manager = Manager::create([
        'name'     => $validated['fName'].' '.$validated['lName'],
        'email'    => $validated['email'],
        'phone'    => $validated['phone'],
        'password' => $hashedPassword,
    ]);

    DB::commit();

    // Send email after successful commit
    Mail::to($user->email)->send(
        new \App\Mail\UserPasswordMail($user, $plainPassword)
    );

    return redirect()
        ->route('managers.index')
        ->with('success', 'Manager created successfully.');

} catch (\Exception $e) {

    DB::rollBack();

    return back()
        ->withInput()
        ->with('error', $e->getMessage());
}
}

    /**
     * Show manager details
     */
    public function show($id)
    {
        $manager = Manager::findOrFail($id);

        return view('layouts.managers.show', compact('manager'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $fmr_manager = Manager::where('id', $id)->select('email', 'id', 'name', 'password', 'phone')->first();
        
        $status = User::where('email', $fmr_manager->email)->first();
        $manager = null; 
        
        if(!$status){
            //create the user if user does not exist
            $manager =  User::create([
                'fName'=>$fmr_manager->name,
                'lName'=>'',
                'email'=>$fmr_manager->email,
                'password'=>$fmr_manager->password,
                'phone'=>$fmr_manager->phone,
                'user_type'=>(int)2,

            ]);
        }else{
            $manager = $status;
        }
        unset($manager->password,$manager->user_type, $manager->id);
        $manager->id = $fmr_manager->id;
        return view('layouts.managers.edit', compact('manager'));
    }

    /**
     * Update manager
     */
   public function update(Request $request, $id)
{
    $manager = Manager::findOrFail($id);

    $validated = $request->validate([
        'lName' => 'required|string|max:255',
        'fName' => 'required|string|max:255',
        'email' => [
            'required',
            'email',
            Rule::unique('managers', 'email')->ignore($manager->id),
        ],
        'phone' => 'nullable|string|max:20',
        'password' => 'nullable|string|min:8|confirmed',
    ]);

    DB::beginTransaction();

    try {

        // Find corresponding user using old email
        $user = User::where('email', $manager->email)->first();

        if ($user) {

            $user->fname = $validated['fName'];
            $user->lname = $validated['lName'];
            $user->email = $validated['email'];

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();
        }

        $manager->name = $validated['fName'].' '.$validated['lName'];
        $manager->email = $validated['email'];
        $manager->phone = $validated['phone'];

        if (!empty($validated['password'])) {
            $manager->password = Hash::make($validated['password']);
        }

        $manager->save();

        DB::commit();

        return redirect()
            ->route('managers.index')
            ->with('success', 'Manager updated successfully.');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()
            ->withInput()
            ->with('error', $e->getMessage());
    }
}
    // Show form to assign apartments to manager
public function viewAssignedApartments($manager_id)
{
    $manager_id = (int) $manager_id;

    // Load apartments only once
    $apartments = ApartmentIdentity::where('apartment_identities.property_manager_id', $manager_id)
        ->join('shelters', 'apartment_identities.shelter_id', '=', 'shelters.id')
        ->join('branch_models', 'apartment_identities.branch_id', '=', 'branch_models.id')
        ->join('location_models', 'apartment_identities.location_models_id', '=', 'location_models.id')
        ->join('managers', 'apartment_identities.property_manager_id', '=', 'managers.id')
        ->select(
            'apartment_identities.*',
            'shelters.name as shelter_name',
            'shelters.id as shelter_id',
            'location_models.name as location_name',
            'location_models.id as location_id',
            'branch_models.name as branch_name',
            'branch_models.id as branch_id',
            'managers.name as manager_name'
        )
        ->get();

    // Load amenities separately
    $amenities = Shelter_Amenities::join(
            'amenities',
            'shelter_amenities.amenity_id',
            '=',
            'amenities.id'
        )
        ->whereIn(
            'shelter_amenities.id_apartment_id',
            $apartments->pluck('id')
        )
        ->where('shelter_amenities.amenity_number', '>', 0)
        ->select(
            'shelter_amenities.id_apartment_id',
            'amenities.name as amenity_name',
            'shelter_amenities.amenity_number'
        )
        ->get()
        ->groupBy('id_apartment_id');

    // Attach amenities to each apartment
    $apartments->each(function ($apartment) use ($amenities) {

        $apartment->amenities = collect();

        if (isset($amenities[$apartment->id])) {

            $apartment->amenities = $amenities[$apartment->id]
                ->map(function ($amenity) {
                    return [
                        'name'   => $amenity->amenity_name,
                        'number' => $amenity->amenity_number
                    ];
                });
        }
    });

    // Group for the view
    $apartments = $apartments->groupBy([
        'location_name',
        'shelter_name'
    ]);

    $locations = null;
    $shelters = null;
    $branches = null;


        $locations = LocationModel::select('id', 'name', 'branch_id')->get();
        $shelters = Shelter::select('id', 'name')->get();
        $branches = BranchModel::select('id', 'name')->get();
    

    return view(
        'layouts.managers.assigned_apartments',
        compact(
            'apartments',
            'manager_id',
            'locations',
            'shelters',
            'branches'
        )
    );
}

    /**
     * Delete manager
     */
    public function destroy($id)
    {
        $manager = Manager::findOrFail($id);

        $manager->delete();

        return redirect()
            ->route('managers.index')
            ->with('success', 'Manager deleted successfully.');
    }
public function loadAssignApartmentPage(Request $request)
{
    $request->validate([
        'location_id' => 'required|exists:location_models,id',
        'shelter_id' => 'required|exists:shelters,id',
    ]);

    $manager_id = (int) $request->manager_id;
    $location_id = (int) $request->location_id;
    $shelter_id = (int) $request->shelter_id;

    $manager = Manager::select('id', 'name')
        ->find($manager_id);

    /*
    |--------------------------------------------------------------------------
    | Amenities grouped by apartment
    |--------------------------------------------------------------------------
    */
    $amenities = Shelter_Amenities::where('location_models_id', $location_id)
        ->where('shelter_id', $shelter_id)
        ->where('amenity_number', '>', 0)
        ->join('amenities', 'shelter_amenities.amenity_id', '=', 'amenities.id')
        ->select(
            'shelter_amenities.id_apartment_id',
            'amenities.name as amenity_name',
            'shelter_amenities.amenity_number'
        )
        ->get()
        ->groupBy('id_apartment_id');

    /*
    |--------------------------------------------------------------------------
    | Apartments
    |--------------------------------------------------------------------------
    */
    $apartments = ApartmentIdentity::where('apartment_identities.location_models_id', $location_id)
        ->where('apartment_identities.shelter_id', $shelter_id)
        ->join('shelters', 'apartment_identities.shelter_id', '=', 'shelters.id')
        ->join('branch_models', 'apartment_identities.branch_id', '=', 'branch_models.id')
        ->join('location_models', 'apartment_identities.location_models_id', '=', 'location_models.id')
        ->leftJoin('managers', 'apartment_identities.property_manager_id', '=', 'managers.id')
        ->leftJoin('estate_owners', 'apartment_identities.landlord_id', '=', 'estate_owners.id')
        ->select(
            'apartment_identities.id as apartment_id',
            'apartment_identities.property_ref as apartment_property_ref',
            'apartment_identities.address as apartment_address',

            'shelters.name as shelter_name',
            'location_models.name as location_name',
            'branch_models.name as branch_name',

            'estate_owners.fName as landlord_fname',
            'estate_owners.lName as landlord_lname',
            'estate_owners.email as landlord_email',

            'managers.name as manager_name'
        )
        ->get();
    

    /*
    |--------------------------------------------------------------------------
    | Attach amenities to apartment
    |--------------------------------------------------------------------------
    */
    $apartments->each(function ($apartment) use ($amenities) {

        $apartmentAmenities = $amenities->get($apartment->apartment_id, collect());

        $apartment->amenities = $apartmentAmenities->map(function ($amenity) {
            return [
                'name'   => $amenity->amenity_name,
                'number' => $amenity->amenity_number,
            ];
        })->values();
    });

    $location_name = optional($apartments->first())->location_name;

    return view(
        'layouts.managers.create_assign_apartments',
        compact(
            'apartments',
            'manager_id',
            'manager',
            'location_name'
        )
    );
}
public function assignApartments(Request $request)
{
    $validated = Validator::make(
        $request->all(),
        [
            'manager_id'   => 'required|exists:managers,id',
            'apartments'   => 'required|array|min:1',
            'apartments.*' => 'exists:apartment_identities,id',
        ]
    );
    

    if ($validated->fails()) {
        return redirect()
            ->back()
            ->withErrors($validated)
            ->withInput();
    }

    $manager_id = (int) $request->manager_id;
    
    $apartment_ids = $request->apartments;



    ApartmentIdentity::whereIn('id', $apartment_ids)
        ->update([
            'property_manager_id' => $manager_id
        ]);


    return redirect()
        ->route(
            'managers.view-assigned-apartments',
            ['manager_id' => $manager_id]
        )
        ->with('success', count($apartment_ids) . ' apartment(s) assigned successfully.');
}
}