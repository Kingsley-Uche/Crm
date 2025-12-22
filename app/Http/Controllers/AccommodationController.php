<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shelter;
use App\Models\Block_Shelter;
use App\Models\EstateOwner;
use App\Models\BlockModel;
use App\Models\ApartmentIdentity;
use App\Models\Amenities;
use App\Models\Shelter_Amenities;
use Illuminate\Support\Facades\Cache;
use App\Models\PaymentTime;
use App\Models\TenantModel as Tenants;
use App\Models\BookingModel;
use Illuminate\Support\Facades\Session;
use App\Models\LocationModel;
class AccommodationController extends Controller
{

public function index()
{
    $user = Session::get('user');
    $permissions = Session::get('permissions');

    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_apartments')))) {
        return redirect()->back()->with(['error' => 'Unauthorized access']);
    }

    $accom = [];
    $stateLocalGvtMap = [];

    // Get booked shelters and their block_model_id
    $bookedShelters = BookingModel::select('shelter_id', 'apartment_id', 'block_model_id', \DB::raw('COUNT(*) as booked_count'))
        ->where('end_date', '>', \Carbon\Carbon::today())->where('is_cancelled', false)
        ->groupBy('shelter_id', 'block_model_id', 'apartment_id')
        ->get()
        ->groupBy('shelter_id');

    // Eager load necessary relationships
    Shelter::with([
        'blockShelters' => function ($query) {
            $query->select('id', 'shelter_id', 'shelter_qty', 'block_models_id', 'estate_owner_id')
                ->where('shelter_qty', '>', 0);
        },
        'blockShelters.estateOwner' => function ($query) {
            $query->select('id', 'fName', 'lName', 'email', 'phones');
        },
        'blockShelters.block' => function ($query) {
            $query->select('id', 'name', 'address', 'location_id');
        },
        'blockShelters.block.location' => function ($query) {
            $query->select('id', 'name');
        }
    ])->chunk(1000, function ($shelters) use (&$accom, $bookedShelters) {
        foreach ($shelters as $shelter) {
            if (!isset($accom[$shelter->id])) {
                $accom[$shelter->id] = [
                    'name' => $shelter->name,
                    'shelter_id' => $shelter->id,
                    'qty' => 0,
                    'block_ids' => [],
                    'blocks' => [],
                    'booked' => 0,
                ];
            }

            foreach ($shelter->blockShelters as $blockShelter) {
                $accom[$shelter->id]['qty'] += (int) $blockShelter->shelter_qty;

                // Check if the shelter has booked shelters
                if (isset($bookedShelters[$shelter->id])) {
                    $bookedShelterData = $bookedShelters[$shelter->id]->firstWhere('block_model_id', $blockShelter->block_models_id);
                    if ($bookedShelterData) {
                        $accom[$shelter->id]['booked'] += $bookedShelterData->booked_count;
                        $accom[$shelter->id]['booked_data'][] = [
                            'block_model_id' => $bookedShelterData->block_model_id,
                            'shelter_id' => $bookedShelterData->shelter_id,
                            'apartment_id' => $bookedShelterData->apartment_id,
                            'booked_count' => $bookedShelterData->booked_count,
                        ];
                    }
                }

                if ($blockShelter->block) {
                    $accom[$shelter->id]['blocks'][] = [
                        'block_name' => $blockShelter->block->name,
                        'block_id' => $blockShelter->block_models_id,
                        'shelter_name' => $shelter->name,
                        'shelter_qty' => $blockShelter->shelter_qty,
                        'address' => $blockShelter->block->address ?? null,
                        'location' => $blockShelter->block->location ? [
                            'id' => $blockShelter->block->location->id,
                            'name' => $blockShelter->block->location->name,
                        ] : null,
                        'landlord_details' => $blockShelter->estateOwner ? [
                            'fName' => $blockShelter->estateOwner->fName,
                            'lName' => $blockShelter->estateOwner->lName,
                            'email' => $blockShelter->estateOwner->email,
                            'phones' => $blockShelter->estateOwner->phones,
                        ] : null,
                        'booked_count' => isset($bookedShelterData) ? $bookedShelterData->booked_count : 0,
                    ];
                }
            }
        }
    });

    return view('layouts.accommodations.index', ['accom' => $accom]);
}

public function accomBlock(Request $request)
{
    $user = Session::get('user');
    $permissions = Session::get('permissions');
    
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_property')))) {
        return redirect()->back()->with('error', 'Unauthorized access to apartments.');
    }
    
    // Validate incoming request data
    $validated = $request->validate([
        'block_id' => 'required|integer|exists:block_shelters,block_models_id',
        'shelter_id' => 'required|integer|exists:block_shelters,shelter_id',
    ]);

    $blockShelter = Block_Shelter::with(['shelter', 'block'])
        ->where([
            'block_models_id' => $validated['block_id'],
            'shelter_id' => $validated['shelter_id']
        ])->firstOrFail();

    // Fetch all amenities once
    $amenity_apartment = Shelter_Amenities::with(['amenitySizes', 'amenities'])
        ->where('block_shelter_id', $blockShelter->id)
        ->where('amenity_number', '>', 0)
        ->get();

    // Fetch apartments
    $apartments = ApartmentIdentity::leftJoin('booking_models', 'booking_models.apartment_id', '=', 'apartment_identities.id')
        ->where([
            'apartment_identities.block_models_id' => $validated['block_id'],
            'apartment_identities.block_shelter_id' => $blockShelter->id
        ])
        ->select(
            'apartment_identities.*',
            'booking_models.end_date as booked_expiry'
        )
        ->orderBy('apartment_identities.id', 'ASC')
        ->paginate(12);

    $apartments->each(function ($apartment) use ($amenity_apartment) {
        $apartment->amenities = $amenity_apartment->where('id_apartment_id', $apartment->id)->values();
    });
    
    // Use caching for amenities and payment frequencies (assumed to change infrequently)
    $amenities = Cache::remember('amenities_list', now()->addHour(), function () {
        return Amenities::all();
    });

    $pay_freq = Cache::remember('payment_time_list', now()->addHour(), function () {
        return PaymentTime::all();
    });

    // Fetch tenants once, avoiding unnecessary fetches
    $tenants = Cache::remember('tenants_list', now()->addHour(), function () {
        return Tenants::select('id', 'full_name')->get();
    });

    // Then pass to view
    return view('layouts.accommodations.view_block', [
        'blockShelter' => $blockShelter,
        'apartments' => $apartments,
        'amenity_apartment' => $amenity_apartment,
        'pay_time' => $pay_freq,
        'tenants' => $tenants,
    ]);
}
}