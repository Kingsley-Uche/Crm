<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookingModel;
use App\Models\ApartmentIdentity;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;




class BookingController extends Controller
{
 
public function book(Request $request)
{
    // Validate the incoming request
    $validated = $this->validateBookingRequest($request);
    
    // Check if the apartment is already booked during the specified period
    $conflictingBooking = $this->checkConflictingBooking($validated);

    if ($conflictingBooking) {
        $availableDate = Carbon::parse($conflictingBooking->end_date)->addDay()->format('Y-m-d');
        return response()->json([
            'success' => false,
            'message' => 'Apartment is not available for these dates. It will be available on ' . $availableDate,
        ], 400);
    }

    // Retrieve the fee for the apartment
    $fee = $this->getApartmentFee($validated['block_id'], $validated['shelter_id']);

    // Create the booking
    $this->createBooking($validated, $fee);

    return response()->json(['message' => 'Booking successfully created', 'success' => true], 201);
}

/**
 * Validate the incoming booking request
 */
protected function validateBookingRequest(Request $request)
{
    return $request->validate([
        'shelter_id' => 'required|numeric|exists:shelters,id',
        'block_id' => 'required|numeric|exists:block_models,id',
        'payment_time_id' => 'required|numeric|exists:apartment_identities,pay_frequency_id',
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'apartment_id' => 'required|numeric|exists:apartment_identities,id',
        'block_shelter_id' => 'required|numeric|exists:block_shelters,id',
        'tenant_id' => 'required|numeric|exists:tenants,id',
    ]);
}

/**
 * Check if there's a conflicting booking during the specified period
 */
protected function checkConflictingBooking($validated)
{
    return BookingModel::where('start_date', '<=', $validated['end_date'])
        ->where('end_date', '>=', $validated['start_date'])
        ->where('block_model_id', $validated['block_id'])
        ->where('shelter_id', $validated['shelter_id'])
        ->where('apartment_id', $validated['apartment_id'])
        ->first();
}

/**
 * Retrieve the fee for the specified apartment
 */
protected function getApartmentFee($blockId, $shelterId)
{
    return ApartmentIdentity::where('block_models_id', $blockId)
        ->where('shelter_id', $shelterId)
        ->first()
        ->fee;
}

/**
 * Create a new booking with validated data
 */
protected function createBooking($validated, $fee)
{
    $user = Auth::user();
    if($user->is_system_admin){
        $validated['booked_by_user_type'] = 'system_admin';
    } else{
        $validated['booked_by_user_type'] = 'user';
    } 
    BookingModel::create([
        'shelter_id' => $validated['shelter_id'],
        'block_model_id' => $validated['block_id'],
        'payment_time_id' => $validated['payment_time_id'],
        'start_date' => $validated['start_date'],
        'end_date' => $validated['end_date'],
        'apartment_id' => $validated['apartment_id'],
        'block_shelter_id' => $validated['block_shelter_id'],
        'booked_by_user_type' => $validated['booked_by_user_type'],
        'booked_by_user_id' => Auth::id(),
        'tenant_id' => $validated['tenant_id'],
        'updated_by_user_id' => Auth::id(),
        'fee' => $fee,
    ]);
}

public function getBooked(){
    $booked = BookingModel::where('is_cancelled', false)->join('tenants', 'tenants.id', '=', 'booking_models.tenant_id')
                    ->join('shelters', 'shelters.id', '=', 'booking_models.shelter_id')
                    ->join('block_models', 'block_models.id', '=', 'booking_models.block_model_id')
                    ->join('estate_owners', 'estate_owners.id', '=', 'block_models.landlord_id')
                    ->select('tenants.full_name', 'tenants.date_of_birth', 'tenants.gender', 'tenants.nationality', 'tenants.state',
                    'tenants.mobile_number', 'tenants.occupant_email as tenant_email', 'booking_models.id as booking_id', 'booking_models.start_date as booked_from',
                    'booking_models.end_date as booked_to', 'booking_models.apartment_id', 'booking_models.booked_by_user_id as booked_by', 
                    'shelters.name as shelter_name', 'block_models.address as block_address', 'block_models.state_name as block_state', 
                    'block_models.lgvt_name as block_lgvt', 'block_models.country_name as block_country', 
                    'estate_owners.fName as landlord_fname','estate_owners.lName as landlord_lname', 'estate_owners.phones as landlord_phones')->orderBy('tenants.id', 'DESC')
                   -> paginate(15);
            return view('layouts.accommodations.view_booked', [
        'booked' => $booked,
    ]);
    

}

public function cancelBooking($id)
{

    // Find the booking by its ID
    $booking = BookingModel::find($id);

    // Check if the booking exists
    if (!$booking) {
        return response()->json([
            'success' => false,
            'message' => 'Booking not found.',
        ], 404);
    }

    // Mark the booking as canceled
    $booking->update(['is_cancelled' => true]);

    // Redirect to the route 'accommodation.booked' with a success message
    return redirect()->route('accommodation.booked')
        ->with('success', 'Booking successfully canceled.');
}




}
