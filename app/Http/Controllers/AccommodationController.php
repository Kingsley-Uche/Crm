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

public function ShelterInLocation($shelter_id, $location_id)
{
    $user = Session::get('user');
    $permissions = Session::get('permissions');
    $shelter_id = (int) $shelter_id;
    $location_id = (int) $location_id;
    
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_property')))) {
        return redirect()->back()->with('error', 'Unauthorized access to apartments.');
    }
$apartments = ApartmentIdentity::select(
    'apartment_identities.*',

    'estate_owners.id as estate_owner_id',
    'estate_owners.fName as estate_owner_fName',
    'estate_owners.lName as estate_owner_lName',
    'estate_owners.email as estate_owner_email',
    'estate_owners.phones as estate_owner_phones',

    'booking_models.id as booking_id',
    'booking_models.start_date as booking_start_date',
    'booking_models.end_date as booking_end_date',
    'booking_models.is_cancelled as booking_is_cancelled',

    'tenants.full_name as tenant_full_name',
    'tenants.id as tenant_id',
    'tenants.gender as tenant_gender',
    'tenants.mobile_number as tenant_mobile_number',
    'tenants.occupant_email as tenant_email'
)
->where('apartment_identities.location_models_id', $location_id)
->where('apartment_identities.shelter_id', $shelter_id)

->leftJoin('estate_owners', 'estate_owners.id', '=', 'apartment_identities.landlord_id')

->leftJoin('booking_models', function ($join) {
    $join->on('booking_models.apartment_id', '=', 'apartment_identities.id')
         ->where('booking_models.is_cancelled', false);
})

->leftJoin('tenants', 'tenants.id', '=', 'booking_models.tenant_id')

->get();
$location_data = LocationModel::where('id', $location_id)->select('id', 'name')->first();

   $shelter_amenities = Shelter_Amenities::join(
        'amenities',
        'amenities.id',
        '=',
        'shelter_amenities.amenity_id'
    )
    ->where('shelter_amenities.location_models_id', $location_id)
    ->where('shelter_amenities.shelter_id', $shelter_id)
    ->select(
        'shelter_amenities.id',
        'shelter_amenities.location_models_id',
        'shelter_amenities.amenity_number',
        'shelter_amenities.amenity_id',
        'shelter_amenities.id_apartment_id',
        'amenities.name as amenity_name'
    )
    ->get();

    
    
    
   
    // Fetch all amenities once
    $amenity_apartment = Shelter_Amenities::with(['amenitySizes', 'amenities'])
        ->where('location_models_id', $location_id)
        ->where('amenity_number', '>', 0)
        ->get();
   

    $apartments->each(function ($apartment) use ($amenity_apartment) {
        $apartment->amenities = $amenity_apartment->where('id_apartment_id', $apartment->id)->values();
        $apartment->booking_status =
        $apartment->booking_is_cancelled ? 'Occupied' : 'Vacant';
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
        'location' => $location_data,
        'apartments' => $apartments,
        'amenity_apartment' => $amenity_apartment,
        'pay_time' => $pay_freq,
        'tenants' => $tenants,
    ]);
}
}