<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shelter_Amenities;
use App\Models\Block_Shelter;
use App\Models\Amenities;
use App\Models\PaymentTime;
use App\Models\ApartmentIdentity;
use Illuminate\Support\Facades\Cache;
use App\Models\TenantModel as Tenant;
use App\Models\ApartmentInfo as ApartInfo;
use App\Models\AmenitySize;
use App\Models\TenancyTypeModel;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ApartmentController extends Controller
{
  

public function createOrUpdate(Request $request) 
  {
        $user = Session::get('user');
        $permissions = Session::get('permissions');
if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_apartments')))) {
    return response()->json([
        'error' => 'Unauthorized',
        'message' => 'You do not have permission to create apartments.'
    ], 403);
}

     
       
    // Validate request
        $validated = $request->validate([
            'block_id' => 'required|exists:block_shelters,block_models_id',
            'shelter_id' => 'required|exists:block_shelters,shelter_id',
            'apart_id' => 'required|exists:apartment_identities,id',
            'fee' => 'required|numeric|min:0.01',
            'pay_freq_id' => 'required|integer|exists:payment_times,id',
            'tenancy_type' => 'required|string|min:1',
            'pro_sco_code' => 'required|string|min:1',
            'property_ref' => 'required|string|min:1',
            'ownership' => 'required|string|min:1',
            'admin_unit' => 'required|string|min:1',
            'unit_name' => 'required|string|min:1',
            'post_code' => 'required|string|min:1',
            'amenities' => 'required|array',
            'amenities.*.id' => 'required|exists:amenities,id',
            'amenities.*.quantity' => 'required|integer|min:0',
        ]);

        try {
            // Start a database transaction
            return DB::transaction(function () use ($validated) {
                // Find the apartment
                $apartment = ApartmentIdentity::where('id', $validated['apart_id'])
                    ->where('block_models_id', $validated['block_id'])
                    ->where('shelter_id', $validated['shelter_id'])
                    ->firstOrFail();

                // Update ApartmentIdentity
                $apartment->update([
                    'fee' => $validated['fee'],
                    'pay_frequency_id' => $validated['pay_freq_id'],
                    'tenancy_type' => $validated['tenancy_type'],
                    'pro_sco_code' => $validated['pro_sco_code'],
                    'property_ref' => $validated['property_ref'],
                    'ownership' => $validated['ownership'],
                    'admin_unit' => $validated['admin_unit'],
                    'unit_name' => $validated['unit_name'],
                    'post_code' => $validated['post_code'],
                ]);

                // Update Shelter_Amenities in bulk
                foreach ($validated['amenities'] as $amenity) {
                    $shelterAmenity = Shelter_Amenities::where([
                        'block_models_id' => $validated['block_id'],
                        'block_shelter_id' => $apartment->block_shelter_id,
                        'amenity_id' => $amenity['id'],
                        'id_apartment_id' => $validated['apart_id'],
                    ])->first();

                    if ($shelterAmenity) {
                        $shelterAmenity->update(['amenity_number' => $amenity['quantity']]);
                    } else {
                        throw ValidationException::withMessages([
                            'amenities' => "Amenity ID {$amenity['id']} does not exist for this apartment.",
                        ]);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Apartment and amenities updated successfully.',
                ], 200);
            });
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to update apartment.',
            ], 500);
        }
    }
    // Delete shelter amenity
    public function destroy($id)
    {
         $user = Session::get('user');
        $permissions = Session::get('permissions');

        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'delete_apartments')))) {
            return redirect()->back()->with('error', 'Unauthorized access to apartments.');
        }
        $amenity = Shelter_Amenities::findOrFail($id);
        $amenity->delete();

        return redirect()->route('apartment.index')->with('success', 'Amenity deleted successfully.');
    }
    
  private function ApartmentInfoCreate($apartment_id)
{
    return ApartmentInfo::create([
        'apartment_id' => $apartment_id,
        'tenancy_type' => null,
        'pro_sco_code' => null,
        'property_ref' => null,
        'ownership' => null,
        'admin_unit' => null, 
        'unit_name' => null,
        'post_code'=>null,
    ]);
}

private function ApartmentInfoUpdate($apartment_id, $data)
{
    return ApartmentInfo::where('apartment_id', $apartment_id)->update($data);
}
 public function UpdateAmenitySize(Request $request)
{
    // Validate the incoming data
    $validated = $request->validate([
        'amenity_sizes' => 'required|array',
        'amenity_sizes.*' => 'required|numeric', // Each amenity_size must be a number
        'amenity_size_id' => 'required|array',
        'amenity_size_id.*' => 'required|integer', // Each amenity_size_id must be an integer
        'apartment_id' => 'required|integer',
        'shelter_id' => 'required|integer',
        'amenity_id' => 'required|integer',
    ]);


     $user = Session::get('user');
        $permissions = Session::get('permissions');

        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_apartments')))) {
            return redirect()->back()->with('error', 'Unauthorized access to apartments.');
        }
    foreach ($validated['amenity_sizes'] as $index => $sizeId) {
        // Check if the amenity_size_id exists in the database
        $amenitySize = AmenitySize::where('id', $sizeId)
            ->where('apartment_id', $validated['apartment_id'])
            ->where('shelter_id', $validated['shelter_id'])
            ->where('amenity_id', $validated['amenity_id'])
            ->first();

        if ($amenitySize) {
            // If the amenity_size_id exists, update the existing record
            $amenitySize->amenity_size = $validated['amenity_sizes'][$index];
            $amenitySize->save();
        } else {
            // If the amenity_size_id doesn't exist, create a new record
            AmenitySize::create([
                'amenity_size' => $validated['amenity_sizes'][$index],
                'amenity_name' => 'Some Name', // Replace with actual name if needed
                'amenity_id' => $validated['amenity_id'],
                'apartment_id' => $validated['apartment_id'],
                'shelter_id' => $validated['shelter_id'],
                'block_models_id' => 1, // Replace with actual block model ID if needed
            ]);
        }
    }

    // Return a JSON response indicating success
    return response()->json([
        'message' => 'Amenity size updated successfully.',
        'success' => true,
        'count_amenity_modal'=>$count,
        ''
    ]);
}


public function index($block_id, $shelter_id)
{
    // Permission check
    $user = Session::get('user');
    $permissions = Session::get('permissions');

    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_apartments')))) {
        return redirect()->back()->with('error', 'Unauthorized access to apartments.');
    }

    // Validate inputs
    if (!is_numeric($block_id) || !is_numeric($shelter_id)) {
        return redirect()->back()->with('error', 'Invalid block or shelter ID provided.');
    }
     Cache::forget('tenancy_types');
      Cache::forget('payment_time_list');
       Cache::forget('tenancy_types');

    // Cache static data
    $amenities = Cache::remember('amenities_list', 3600, fn () => Amenities::all());
    $pay_freq = Cache::remember('payment_time_list', 3600, fn () => PaymentTime::all());
    $tenancyTypes = Cache::remember('tenancy_types', 3600, fn () => TenancyTypeModel::all());

    // Eager load block shelter
    $blockShelter = Block_Shelter::with(['estateOwner', 'shelter', 'block'])
        ->where('block_models_id', $block_id)
        ->where('shelter_id', $shelter_id)
        ->first();

    if (!$blockShelter) {
        return redirect()->back()->with('error', 'No block shelter found for the specified block and shelter.');
    }

    // Fetch apartments with pagination
    $apartments = ApartmentIdentity::where('shelter_id', $shelter_id)
        ->where('block_models_id', $block_id)
        ->orderBy('id')
        ->paginate(6); // Paginate with 6 items per page

    // Check sync if apartments are empty
    if ($apartments->isEmpty()) {
        $this->check_sync($block_id, $shelter_id, $blockShelter->shelter_qty, $blockShelter->id);
    }

    // Get Amenity Sizes
    $amenity_sizes = AmenitySize::where('shelter_id', $shelter_id)
        ->where('block_models_id', $block_id)
        ->get();

    // Fetch and format shelter amenities
    $amenity_apartment = Shelter_Amenities::with(['amenity.amenitySizes']) // Use 'amenity' as per model
        ->where('block_models_id', $block_id)
        ->where('block_shelter_id', $shelter_id)
        ->get()
        ->map(function ($shelterAmenity) use ($blockShelter) {
            $amenity = $shelterAmenity->amenity;
            if (!$amenity) return [];

            return [
                'amenity_name' => $amenity->name,
                'amenity_number' => $shelterAmenity->amenity_number,
                'amenity_id' => $shelterAmenity->amenity_id,
                'amenity_apart_id' => $shelterAmenity->id_apartment_id,
                'block_shelter_id' => $blockShelter->id,
                'shelter_name' => $blockShelter->shelter->name,
                'amenity_sizes' => $amenity->amenitySizes->map(function ($size) use ($amenity) {
                    return [
                        'size' => $size->amenity_size,
                        'apartment_id' => $size->apartment_id,
                        'block_models_id' => $size->block_models_id,
                        'shelter_id' => $size->shelter_id,
                        'amenity_name' => $amenity->name,
                        'amenity_size_id' => $size->id,
                        'amenity_id' => $size->amenity_id,
                    ];
                })->toArray(),
            ];
        })->filter()->values();

    // Choose view
    $viewName = !$amenity_apartment->isEmpty() ? 'layouts.apartment.edit' : 'layouts.apartment.index';
    return view($viewName, [
        'blockShelter' => $blockShelter,
        'block_id' => $block_id,
        'amenities' => $amenities,
        'pay_time' => $pay_freq,
        'amenity_apartment' => $amenity_apartment,
        'apartments' => $apartments,
        'tenancy_type' => $tenancyTypes,
        'amenity_sizes' => $amenity_sizes,
    ]);
}

    
public function edit($block_id, $shelter_id, $apart_id)
{
    // Permission check
    $user = Session::get('user');
    $permissions = Session::get('permissions');

    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_apartments')))) {
        return redirect()->back()->with('error', 'Unauthorized access to apartments.');
    }

    // Validate inputs
    if (!is_numeric($block_id) || !is_numeric($shelter_id) || !is_numeric($apart_id)) {
        return redirect()->back()->with('error', 'Invalid block, shelter, or apartment ID provided.');
    }

    try {
        // Cache static data
        $amenities = Cache::remember('amenities_list', 3600, fn () => Amenities::all());
        $pay_freq = Cache::remember('payment_time_list', 3600, fn () => PaymentTime::all());
        $tenancyTypes = Cache::remember('tenancy_types', 3600, fn () => TenancyTypeModel::all());

        // Fetch block shelter with related data
        $blockShelter = Block_Shelter::with(['estateOwner', 'shelter', 'block'])
            ->where('block_models_id', $block_id)
            ->where('shelter_id', $shelter_id)
            ->first();

        if (!$blockShelter) {
            return redirect()->back()->with('error', 'No block shelter found for the specified block and shelter.');
        }

        // Fetch the apartment
        $apartment = ApartmentIdentity::where('id', $apart_id)
            ->where('block_models_id', $block_id)
            ->where('shelter_id', $shelter_id)
            ->first();

        if (!$apartment) {
            return redirect()->back()->with('error', 'Apartment not found.');
        }

        // Fetch shelter amenities for the apartment
        $amenity_apartment = Shelter_Amenities::with(['amenity.amenitySizes'])
            ->where('block_models_id', $block_id)
            ->where('block_shelter_id', $blockShelter->id)
            ->where('id_apartment_id', $apart_id)
            ->get()
            ->map(function ($shelterAmenity) use ($blockShelter) {
                $amenity = $shelterAmenity->amenity;
                if (!$amenity) return null;

                return [
                    'amenity_name' => $amenity->name,
                    'amenity_number' => $shelterAmenity->amenity_number,
                    'amenity_id' => $shelterAmenity->amenity_id,
                    'amenity_apart_id' => $shelterAmenity->id_apartment_id,
                    'block_shelter_id' => $blockShelter->id,
                    'shelter_name' => $blockShelter->shelter->name,
                    'amenity_sizes' => $amenity->amenitySizes
                        ->where('apartment_id', $shelterAmenity->id_apartment_id)
                        ->map(function ($size) use ($amenity) {
                            return [
                                'size' => $size->amenity_size,
                                'apartment_id' => $size->apartment_id,
                                'block_models_id' => $size->block_models_id,
                                'shelter_id' => $size->shelter_id,
                                'amenity_name' => $amenity->name,
                                'amenity_size_id' => $size->id,
                                'amenity_id' => $size->amenity_id,
                            ];
                        })->toArray(),
                ];
            })->filter()->values();

        // Fetch amenity sizes
        $amenity_sizes = AmenitySize::where('shelter_id', $shelter_id)
            ->where('block_models_id', $block_id)
            ->where('apartment_id', $apart_id)
            ->get();

        // Return the edit view with the necessary data
        return view('layouts.apartment.edit', [
            'blockShelter' => $blockShelter,
            'block_id' => $block_id,
            'shelter_id' => $shelter_id,
            'apartment' => $apartment,
            'amenities' => $amenities,
            'pay_time' => $pay_freq,
            'amenity_apartment' => $amenity_apartment,
            'tenancy_type' => $tenancyTypes,
            'amenity_sizes' => $amenity_sizes,
        ]);
    } catch (\Exception $e) {
        Log::error('Error fetching apartment data for edit: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to load apartment data for editing.');
    }
}
}


