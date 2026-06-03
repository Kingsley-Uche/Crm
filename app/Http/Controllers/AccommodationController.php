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

    // Get booked shelters and their block_model_id
$bookedApartments = BookingModel::where('end_date', '>', now())
    ->where('is_cancelled', false)
    ->pluck('apartment_id')
    ->toArray();

$accom = Shelter::with([
    'apartments.location'
])
->where('is_active', 1)
->get()
->map(function ($shelter) use ($bookedApartments) {

    $locations = $shelter->apartments
        ->groupBy('location_id')
        ->map(function ($apartments) use ($bookedApartments) {

            return [
                'location_id'   => $apartments->first()->location?->id,
                'location_name' => $apartments->first()->location?->name ?? 'Unknown',
                'count'         => $apartments->count(),
                'booked'        => $apartments
                    ->whereIn('id', $bookedApartments)
                    ->count(),
            ];
        })
        ->values();

    return [
        'shelter_id'   => $shelter->id,
        'name'         => $shelter->name,
        'qty'          => $shelter->apartments->count(),
        'booked'       => $shelter->apartments
                            ->whereIn('id', $bookedApartments)
                            ->count(),
        'locations'    => $locations,
    ];
});

return view('layouts.accommodations.index', compact('accom'));

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