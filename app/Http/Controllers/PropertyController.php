<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlockModel;
use App\Models\Amenities;
use App\Models\Shelter;
use App\Models\EstateOwner;
use App\Models\States;
use App\Models\LocalGvt;
use App\Models\Block_Shelter;
use App\Models\ApartmentIdentity;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Shelter_Amenities;
use App\Models\ApartmentInfo;
use App\Models\AmenitySize;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MultiTableImport;
use App\Models\LocationModel;
use App\Models\BranchModel;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
class PropertyController extends Controller
{
    // Create (store)
    public function Create(Request $request)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_property')))) {
            return redirect()->back()->with('error', 'Unauthorized access to create property.');
        }
        $branches = BranchModel::select('id', 'name')->get();
        $locations = LocationModel::select('id', 'name', 'branch_id')->get();
        $amenities = Amenities::select('id', 'name')->where('is_active', 1)->get();
        $shelters = Shelter::select('id', 'name')->where('is_active', 1)->get();
        $landlords = EstateOwner::select('id', 'fName', 'lName', 'email')->get();
        
    
        session(['shelters' => $shelters]);
    

        return view('layouts.property_manager.create', compact('branches', 'amenities', 'shelters', 'landlords','locations'));
    }

 



public function storeApartment(Request $request)
{
    $user = Session::get('user');
    $permissions = Session::get('permissions');
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_property')))) {
        return redirect()->back()->with('error', 'Unauthorized access to create property.');
    }
    

    $validated = $request->validate([
        'address' => 'required|string|max:255',
        'shelter_qty' => 'required|array',
        'landlord_id'=>'required|integer',
        'location_id' => 'required|string|exists:location_models,id',
        'branch_id' => 'required|integer|exists:branch_models,id',
    ]);

    $validated['address'] = strtolower($validated['address']);



    $amenities = Amenities::all();
    $apartmentUtil = new ApartmentIdentity();

    
 foreach ($validated['shelter_qty'] as $shelterId => $quantity) {

    if ((int)$quantity <= 0) {
        continue;
    }

    for ($i = 0; $i < (int)$quantity; $i++) {

        $unique_code = $apartmentUtil->generateUniqueCode(
            $validated['branch_id'],
            $validated['location_id']
        );

        $apartment_identity = ApartmentIdentity::create([
            'branch_id'          => $validated['branch_id'],
            'location_models_id' => $validated['location_id'],
            'landlord_id'        => $validated['landlord_id'],
            'address'            => $validated['address'],
            'unique_code'        => $unique_code,
            'shelter_id'         => $shelterId,
        ]);

        foreach ($amenities as $amenity) {

            Shelter_Amenities::updateOrCreate(
                [
                    'branch_id' => $validated['branch_id'],
                    'location_models_id' => $validated['location_id'],
                    'shelter_id' => $validated['shelter_id'],
                ],
                [ 'id_apartment_id' => $apartment_identity->id,
                    'amenity_id' => $amenity->id,
                    'amenity_number' => 0,
                ]
            );

            AmenitySize::updateOrCreate(
                [
                    'branch_id' => $validated['branch_id'],
                    'location_models_id' => $validated['location_id'],
                    'shelter_id' => $shelterId,
                    'amenity_id' => $amenity->id,
                    'apartment_id' => $apartment_identity->id,
                ],
                [
                    'amenity_name' => $amenity->name,
                    'amenity_size' => 0,
                ]
            );
        }
    }
}
    Cache::forget('amenities_list');

    return redirect()->route('property.index')->with('success', 'Block created successfully!');
}


private function validateBlockData(Request $request)
{
    return $request->validate([
        'name' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'landlord_id' => 'required|integer|exists:estate_owners,id',
        'shelter_qty' => 'required|array',
        'shelter_qty.*' => 'required|integer|min:0',
        'location_id' => 'required|string',
    ]);
}



private function convertToLowercase(array &$validated)
{
    $validated['name'] = strtolower($validated['name']);
    $validated['address'] = strtolower($validated['address']);
}

private function createBlock(array $validated, $locationData)
{
    return BlockModel::create([
        'name' => $validated['name'],
        'address' => $validated['address'],
        'landlord_id' => $validated['landlord_id'],
        'location_id'=>$validated['location_id'],
    ]);
}

private function handleShelters($block, array $shelterQty, $landlordId)
{
    // Fetch amenities once to avoid multiple queries
    $amenities = Amenities::all();

    foreach ($shelterQty as $shelterId => $quantity) {
        $this->createBlockShelterAndApartments($block, $shelterId, $quantity, $landlordId, $amenities);
    }
}

private function createBlockShelterAndApartments($block, $shelterId, $quantity, $landlordId, $amenities)
{
    // Create block shelter
    $blockShelter = $block->shelters()->create([
        'shelter_qty' => $quantity,
        'estate_owner_id' => $landlordId,
        'shelter_id' => $shelterId,
    ]);

    // Generate apartment identities and update amenities
    for ($i = 0; $i < $quantity; $i++) {
        $this->createApartmentAndAssignAmenities($block, $blockShelter, $shelterId, $amenities);
    }
}

private function createApartmentAndAssignAmenities($block, $blockShelter, $shelterId, $amenities)
{
    // Generate unique apartment code
    $unique_code = (new ApartmentIdentity())->generateUniqueCode($block->id, $blockShelter->id);

    // Create apartment identity
    $apartment = ApartmentIdentity::create([
        'branch_id' => $validated['branch_id'],
        'location_models_id' => $validated['location_id'],
        'shelter_id' => $shelterId,
        'unique_code' => $unique_code,
        'landlord_id' => $validated['landlord_id'],
    ]);

    // Assign amenities to the apartment
    foreach ($amenities as $amenity) {
        Shelter_Amenities::updateOrCreate(
            [
                'branch_id' => $validated['branch_id'],
                'location_models_id' => $validated['location_id'],
                'shelter_id' => $shelterId,
                'amenity_id' => $amenity->id,
                'apartment_id' => $apartment->id,
            ],
            [
                'amenity_number' => 0, // or any default value
            ]
        );
    }
}


private function check_sync($block_id, $shelter_id, $block_shelter_id, $qty)
{
    // Generate ApartmentIdentity records based on quantity
    for ($i = 0; $i < $qty; $i++) {
        $apartmentId = new ApartmentIdentity();
        
        // Generate the unique code using the method in the ApartmentIdentity model
        $unique_code = $apartmentId->generateUniqueCode($block_id, $block_shelter_id);
        
        // Create the new ApartmentIdentity record
        ApartmentIdentity::create([
            'branch_id' => $block_id,
            'location_models_id' => $block_shelter_id,
            'unique_code' => $unique_code,
            'shelter_id'=>$shelter_id,
        ]);
    }

    return true;
}
  
public function blockIndex()
{
    $user = Session::get('user');
    $permissions = Session::get('permissions'); 
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_property')))) {
        return redirect()->back()->with('error', 'Unauthorized access to property management.');
    }   
    
    $locations = LocationModel::select(
        'location_models.id as location_id',
        'location_models.branch_id',
        'location_models.name as location_name',
        'shelters.id as shelter_id',
        'shelters.name as shelter_name',
        'branches.name as branch_name'
    )
    ->selectRaw('COUNT(apartment_identities.id) as total_apartments')
    ->leftJoin(
        'apartment_identities',
        'apartment_identities.location_models_id',
        '=',
        'location_models.id'
    )
    ->leftJoin(
        'shelters',
        'apartment_identities.shelter_id',
        '=',
        'shelters.id'
    )->leftJoin(
        'branch_models as branches',
        'location_models.branch_id',
        '=',
        'branches.id'
    )
    ->groupBy(
        'location_models.id',
        'location_models.branch_id',
        'location_models.name',
        'shelters.id',
        'shelters.name',
        'branches.name'
    )
    ->orderBy('location_models.name')
    ->get();
   $result = [];

foreach ($locations as $row) {

    $locationId = $row->location_id;

    if (!isset($result[$locationId])) {
        $result[$locationId] = [
            'location_id'   => $row->location_id,
            'branch_id'     => $row->branch_id,
            'branch_name'   => $row->branch_name,
            'location_name' => $row->location_name,
            'shelters'      => [],
        ];
    }

    if ($row->shelter_id) {
        $result[$locationId]['shelters'][] = [
            'shelter_id'       => $row->shelter_id,
            'shelter_name'     => $row->shelter_name,
            'total_apartments' => $row->total_apartments,
        ];
    }
}

$locations = array_values($result);


return view('layouts.property_manager.index', [
    'locations' => $locations,
    
]);
}


    // Show a single block (read)
public function showLocation($location_id, $shelter_id)
{
    $user = Session::get('user');
    $permissions = Session::get('permissions');

    // Authorization check
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_property')))) {
        return redirect()->back()->with('error', 'Unauthorized access to property management.');
    }

    // Fetch apartments with all relationships in single queries (No N+1)
    $apartments = ApartmentIdentity::with([
        'landlord',
        'shelter',
        'location',
        'shelterAmenities.amenities'           // Eager load amenity sizes
    ])
    ->where('location_models_id', $location_id)
    ->where('shelter_id', $shelter_id)
    ->get();



    // Fetch supporting data
    $shelters  = Shelter::all();
    $landlords = EstateOwner::all();
    $locations = LocationModel::all();

    return view('layouts.property_manager.edit', [
        'apartments'   => $apartments,
        'locations'    => $locations,
        'shelters'     => $shelters,
        'landlords'    => $landlords,
        'location_id'  => $location_id,
        'shelter_id'   => $shelter_id,
    ]);
}

public function ApartmentUpdate(Request $request, $id)
{
    $user = Session::get('user');
    $permissions = Session::get('permissions');

    if (
        !$user ||
        (!$user->system_admin &&
        (!$permissions || !$permissions->contains('slug', 'update_property')))
    ) {
        return redirect()->back()
            ->with('error', 'Unauthorized access to property management.');
    }

    // Validate incoming request
    $validated = $request->validate([
        'property_ref' => 'required|string|max:50',
        'address'      => 'required|string|max:255',
        'landlord_id'  => 'required|integer',
        'location_id'  => 'required|integer',
        'unit_number'  => 'required|string|max:50',
        'shelter_id'   => 'required|integer|exists:shelters,id',

        // IMPORTANT FIX: allow empty amenities safely
        'amenities'    => 'nullable|array',
        'amenities.*'  => 'nullable|integer|min:0',
    ]);

    $apartment = ApartmentIdentity::findOrFail($id);

    // Normalize text
    $propertyRef = strtolower(trim($validated['property_ref']));
    $address     = strtolower(trim($validated['address']));

    // =========================
    // 1. UPDATE APARTMENT
    // =========================
    $apartment->update([
        'property_ref' => $propertyRef,
        'address'      => $address,
        'landlord_id'  => $validated['landlord_id'],
        'location_id'  => $validated['location_id'],
        'shelter_id'   => $validated['shelter_id'],
        'unit_number'  => $validated['unit_number'],
    ]);

    // =========================
    // 2. UPDATE AMENITIES
    // =========================
    if ($request->has('amenities')) {

        foreach ($request->amenities as $amenity_id => $qty) {

            Shelter_Amenities::updateOrCreate(
                [
                    'id_apartment_id' => $apartment->id,
                      'amenity_id'   => $amenity_id,
                    
                ],
                [
                    'amenity_number' => (int) $qty,
                  
                    'location_models_id' => $validated['location_id'],
                    'shelter_id' => $validated['shelter_id'],
                ]
            );
        }

        Cache::forget('amenities_list');
    }

    return redirect()
        ->route('property.index')
        ->with('success', 'Apartment updated successfully!');
}


    public function blockDestroy($id)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions'); 
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'delete_property')))) {
            return redirect()->back()->with('error', 'Unauthorized access to property management.');
        }
        $block = BlockModel::findOrFail($id);
        $block->delete();

        return redirect()->route('property.index')->with('success', 'Block deleted successfully!');
    }

    // Fetch LGVT (Local Government Area)



    // Additional private method to save block shelter (corrected)
    private function saveBlockShelter(array $data)
    {
        Block_Shelter::create($data);
    }

    public function search(Request $request)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_property')))) {
            return redirect()->back()->with('error', 'Unauthorized access to property management.');
        }
        // Fixing the typo
        $query = strtolower($request->get('query'));

        // Perform the query
        $blocks = BlockModel::with(['landlord', 'state', 'localGovernment', 'shelters'])
            ->whereRaw('LOWER(name) LIKE ?', ['%' . $query . '%']) // Ensure case-insensitive search
            ->paginate(8); // Paginate results

        // Check if the request is an AJAX request
        if ($request->ajax()) {
            return response()->json([
                'blocks' => view('layouts.property_manager.partials.block_list', compact('blocks'))->render(),
                'pagination' => (string) $blocks->links('pagination::bootstrap-4')
            ]);
        }

        // Return the view if not an AJAX request
        return view('layouts.property_manager.index', compact('blocks'));
    }
    
    public function loadImport(){
    $user = Session::get('user');
    $permissions = Session::get('permissions'); 
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_property')))) {
        return redirect()->back()->with('error', 'Unauthorized access to import properties.');
    }
    return view('layouts.property_manager.import');
}

    public function import(Request $request)
{
    // Validate the file type
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv|max:10240', // max size 10MB
    ]);

    try {
        // Perform the import
     Excel::import(new MultiTableImport, $request->file('file'));
        Cache::forget('amenities_list');
        // Return a JSON response indicating success
        return response()->json([
            'message' => 'Properties imported successfully!',
            'status' => 'success',
            'success'=>true,
        ], 200);
    } catch (\Exception $e) {
        // Return a JSON response indicating failure
        return response()->json([
            'message' => 'Failed to import properties.',
            'status' => 'error',
            'error' => $e->getMessage(),
        ], 500);
    }
}
}
