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
        $blocks = BlockModel::all();
        $amenities = Amenities::all();
        $shelters = Shelter::all();
        $landlords = EstateOwner::all();
        
        $locations = LocationModel::all();
        session(['shelters' => $shelters]);
    

        return view('layouts.property_manager.create', compact('blocks', 'amenities', 'shelters', 'landlords','locations'));
    }

 



public function storeBlock(Request $request)
{
    $user = Session::get('user');
    $permissions = Session::get('permissions');

    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_property')))) {
        return redirect()->back()->with('error', 'Unauthorized access to create property.');
    }
    

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'shelter_qty' => 'required|array',
        'landlord_id'=>'required|integer',
        'location_id' => 'required|string|exists:location_models,id',
    ]);

    $validated['name'] = strtolower($validated['name']);
    $validated['address'] = strtolower($validated['address']);

    // Create the block
    $block = BlockModel::create([
        'name' => $validated['name'],
        'address' => $validated['address'],
        'landlord_id' => $validated['landlord_id'],
        'location_id' => $validated['location_id'],
    ]);

    $amenities = Amenities::all();
    $apartmentUtil = new ApartmentIdentity();

    // Process each shelter
    foreach ($validated['shelter_qty'] as $shelterId => $quantity) {
        $blockShelter = $block->shelters()->create([
            'shelter_qty' => $quantity,
            'estate_owner_id' => $validated['landlord_id'],
            'shelter_id' => $shelterId,
        ]);

        // Create the number of apartments equal to shelter_qty
        for ($i = 0; $i < (int) $quantity; $i++) {
            $unique_code = $apartmentUtil->generateUniqueCode($block->id, $blockShelter->id);

            $apartment_identity = ApartmentIdentity::create([
                'block_models_id' => $block->id,
                'block_shelter_id' => $blockShelter->id,
                'unique_code' => $unique_code,
                'shelter_id' => $shelterId,
            ]);

            foreach ($amenities as $amenity) {
                // Store Shelter Amenities
                Shelter_Amenities::updateOrCreate(
                    [
                        'block_models_id' => $block->id,
                        'block_shelter_id' => $blockShelter->id,
                        'amenity_id' => $amenity->id,
                        'id_apartment_id' => $apartment_identity->id,
                    ],
                    [
                        'amenity_number' => 0,
                    ]
                );

                // Store Amenity Size
                AmenitySize::updateOrCreate(
                    [
                        'block_models_id' => $block->id,
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
        'block_models_id' => $block->id,
        'block_shelter_id' => $blockShelter->id,
        'unique_code' => $unique_code,
        'shelter_id' => $shelterId,
    ]);

    // Assign amenities to the apartment
    foreach ($amenities as $amenity) {
        Shelter_Amenities::updateOrCreate(
            [
                'block_models_id' => $block->id,
                'block_shelter_id' => $blockShelter->id,
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
            'block_models_id' => $block_id,
            'block_shelter_id' => $block_shelter_id, // Use block_shelter_id instead of shelter_id
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
    // Eager load estate owners and shelters to avoid N+1 query issue
    $blocks = BlockModel::with(['landlord:id,fName,lName,email,phones', 'shelters:shelter_id,shelter_qty', 'location:id,name'])
        ->select(
            'block_models.id as block_model_id',
            'block_models.name as block_title', 
            'block_models.address as block_address',
            'block_models.landlord_id',
            'block_models.location_id'
        )
        ->paginate(20);

    // Fetch and cache local government and state names to avoid repeated queries
    $stateLocalGvtMap = [];
    foreach ($blocks as $block) {
         $block->shelters = Shelter::join('block_shelters', 'block_shelters.shelter_id', '=', 'shelters.id')
            ->where('block_shelters.block_models_id', $block->block_model_id)
            ->select('shelters.name as shelter_name', 'block_shelters.shelter_qty', 'block_shelters.shelter_id as shelter_id')
            ->get();
        
    }
    $cat = Shelter::all();

    // Return the view with the blocks and shelter categories
    return view('layouts.property_manager.index', compact('blocks', 'cat')); 
}


    // Show a single block (read)
public function showBlock($id)
{
    $user = Session::get('user');
    $permissions = Session::get('permissions');
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_property')))) {
        return redirect()->back()->with('error', 'Unauthorized access to property management.');
    }

    // Fetch block with related shelters and landlord in a single query
    $block = BlockModel::with(['shelters', 'landlord', 'location'])->findOrFail($id);
    // Fetch necessary data
    $shelters = Shelter::all(); 
    $landlords = EstateOwner::all(); 

    // Fetch countries from JSON file
    $locations = LocationModel::all();
    

 

    // Return the view with compacted data
    return view('layouts.property_manager.edit', compact('block', 'shelters', 'landlords', 'locations'));
}


public function blockUpdate(Request $request, $id)
{
    $user = Session::get('user');
    $permissions = Session::get('permissions'); 
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_property')))) {
        return redirect()->back()->with('error', 'Unauthorized access to property management.');
    }
    // Validate incoming request
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'landlord_id' => 'required|integer',
        'location_id'=>'required|integer',
        'shelter_qty' => 'required|array',
        'shelter_qty.*' => 'required|integer|min:0',
    ]);

    // Convert name and address to lowercase for consistency
    $validated['name'] = strtolower($validated['name']);
    $validated['address'] = strtolower($validated['address']);

    // Fetch the block by ID, or throw 404 if not found
    $block = BlockModel::findOrFail($id);

    // Update block details
    $block->update([
        'name' => $validated['name'],
        'address' => $validated['address'],
        'landlord_id' => $validated['landlord_id'],
        'location_id'=>$validated['location_id'],
    ]);

    // Fetch shelters for the block in a keyed collection for efficient lookups
    $shelters = Block_Shelter::where('block_models_id', $id)->get()->keyBy('shelter_id');

    // Loop through the shelter quantities and update them accordingly
    foreach ($validated['shelter_qty'] as $shelterId => $quantity) {
        if (isset($shelters[$shelterId])) {
            $shelter = $shelters[$shelterId];

            // If the new quantity is greater than the existing, sync additional apartments
            if ($quantity > $shelter->shelter_qty) {
                $additional_qty = $quantity - $shelter->shelter_qty;
                // Sync new apartments with unique codes
                $this->check_sync($block->id, $shelterId, $shelter->id, $additional_qty);
            }

            // Update shelter quantity
            $shelter->update(['shelter_qty' => $quantity]);
        } else {
            // If the shelter ID does not exist for the block, return an error
            return back()->withErrors([
                'shelter_qty' => "Shelter ID {$shelterId} not found in this block."
            ]);
        }
    }
    Cache::forget('amenities_list');

    // Optionally return a success response
    return redirect()->route('property.index')->with('success', 'Block updated successfully!');

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
