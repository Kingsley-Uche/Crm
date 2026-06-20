<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookingModel;
use App\Models\ApartmentIdentity;
use App\Models\RentAccount;
use App\Models\RentCycle;
use App\Models\TenantModel as Tenant;
use App\Http\Controllers\InvoiceController;
use App\Mail\RentRenewed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RentController extends Controller
{
    public function ViewApartments()
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');

        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_apartments')))) {
            return redirect()->back()->with('error', 'Unauthorized access to apartments.');
        }

        $apartments = ApartmentIdentity::with([
            'bookStatus',
            'shelter:id,name',
            'AmenitySize:id,amenity_name,amenity_size,apartment_id'
        ])
        ->where('unit_number', '>', 0)
        ->get();

        return view('layouts.rent.index', compact('apartments'));
    }

    public function createRentAccount()
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');

        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_rent')))) {
            return redirect()->back()->with('error', 'Unauthorized access to rent creation.');
        }

        $tenants = Tenant::select('id', 'full_name', 'gender')->get();

        $apartments = ApartmentIdentity::with([
            'shelter:id,name',
            'AmenitySize:id,amenity_name,amenity_size,apartment_id'
        ])
        ->where('unit_number', '>', 0)
        ->get();

        return view('layouts.rent.create', compact('tenants', 'apartments'));
    }

   public function store(Request $request)
{
    $user = Session::get('user');
    $permissions = Session::get('permissions');

    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_rent')))) {
        return redirect()->back()->with('error', 'Unauthorized access to rent creation.');
    }

    $validated = $request->validate([
        'tenant_id'         => ['required', 'exists:tenants,id'],
        'apartment_id'      => ['required', 'exists:apartment_identities,id'],
        'unit_number'       => ['required', 'string'],
        'start_date'        => ['required', 'date'],
        'end_date'          => ['required', 'date', 'after_or_equal:start_date'],
        'rent_fee'          => ['required', 'numeric', 'min:0'],
        'payment_made'      => ['required', 'numeric', 'min:0'],
        'account_type'      => ['required', 'string'],
        'escalation_policy' => ['required', 'string'],
        'payment_method'    => ['required', 'string'],
    ]);

$apartment= ApartmentIdentity::where('id',$validated['apartment_id'])
->select('location_models_id', 'branch_id', 'property_manager_id', 'id as apartment_id')->first();


    $validated['duration_months'] = floor(Carbon::parse($validated['start_date'])
        ->floatDiffInMonths(Carbon::parse($validated['end_date'])));

    $validated['created_by'] = Auth::id();
    

    if ($this->hasActiveAccount($validated['apartment_id'])) {
        return back()->withInput()->with('error', 'An active rent account already exists for the selected apartment.');
    }

    $invoice = new InvoiceController();
    $tenant = Tenant::where('id', $validated['tenant_id'])->select('id', 'full_name', 'mobile_number', 'occupant_email')->first();

    try {
         $request->merge([
    'branch_id'   =>$apartment->branch_id,
    'location_id' =>$apartment->location_models_id,
    'paid_amount' => $validated['payment_made'],
    'description' => 'Invoice for Rent Payment from ' .
                     $validated['start_date'] . ' to ' .
                     $validated['end_date'] . ' for ' .
                     $tenant['full_name'] .
                     '<br>Number of Months: ' .
                     $validated['duration_months'],//round down
    'amount'      => $validated['rent_fee'],
    'due_date'    => now(),

    'items' => [
        [
            'name'        => 'Rent Renewal Invoice for ' .
                             $validated['start_date'] . ' to ' .
                             $validated['end_date'] . ' for ' .
                             $tenant->full_name.
                              '<br>Number of Months: ' .
                     $validated['duration_months'],//round down
            'qty'         => 1,
            'unit_charge' => $validated['rent_fee'],
            'amount'      => $validated['rent_fee'],
        ]
    ]
]);


        DB::transaction(function () use (&$validated,$request,$invoice) {
            $rentAccount = RentAccount::create($validated);
            $validated['rent_account_id'] = $rentAccount->id;

            $this->createBooking($validated, $validated['rent_fee']);
          
            
          

            RentCycle::create($validated);
                $invoice->store($request);
            //generate invoice
        });

        return redirect()->route('rent.active')->with('success', 'Rent account created successfully.');

    } catch (\Exception $e) {
        // Explicitly logging the full stack trace and request details for debugging
        Log::error('Rent account creation failed during DB transaction.', [
            'exception_message' => $e->getMessage(),
            'user_id'           => Auth::id(),
            'tenant_id'         => $validated['tenant_id'] ?? null,
            'apartment_id'      => $validated['apartment_id'] ?? null,
        ]);

        return back()->withInput()->with('error', 'Something went wrong while creating the rent account. Please try again.');
    }
}

    private function hasActiveAccount($apartment_id): bool
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions'); 
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_rent')))) {
            return false; // Unauthorized access
        }
        return RentAccount::where('apartment_id', $apartment_id)
            ->where('status', 'active')
            ->exists();
    }

    public function index()
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_rent')))) {
            return redirect()->back()->with('error', 'Unauthorized access to rent accounts.');
        }
        $rent_accounts = $this->getAccountsByStatus('active');
        $rent_accounts->title = 'Active Accounts';
        $rent_accounts->url = 'rent_accounts.transactions';

        return view('layouts.rent.account_index', compact('rent_accounts'));
    }

    public function inactive()
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_rent')))) {
            return redirect()->back()->with('error', 'Unauthorized access to rent accounts.');
        }
        $rent_accounts = $this->getAccountsByStatus('!=', 'active');
        $rent_accounts->title = 'Inactive Accounts';
        $rent_accounts->url = 'rent_accounts_inactive.transactions';

        return view('layouts.rent.account_index', compact('rent_accounts'));
    }

    private function getAccountsByStatus($operator = '=', $status = 'active')
    {
        $accounts = RentAccount::with([
            'rentCycles:id,rent_account_id,apartment_id,tenant_id,rent_fee,payment_method,payment_made',
            'apartment:id,tenancy_type,pro_sco_code,property_ref,address,post_code,unique_code',
            'tenant:id,full_name,occupant_email,date_of_birth'
        ])
        ->select('id', 'tenant_id', 'apartment_id', 'unit_number', 'start_date', 'account_type', 'status')
        ->where('status', $operator, $status)
        ->get();

        foreach ($accounts as $account) {
            $this->calculateAccountTotals($account);
            $account->color = $status === 'active' ? 'success' : 'danger';
        }

        return $accounts;
    }

    private function calculateAccountTotals(&$account)
    {
        $account->total_fee = $account->rentCycles->sum('rent_fee');
        $account->total_paid = $account->rentCycles->sum('payment_made');
        $account->balance = $account->total_paid - $account->total_fee;
    }

    public function deactivate($id)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');

        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_rent')))) {
            return redirect()->back()->with('error', 'Unauthorized access to rent accounts.');
        }

        $rentAccount = RentAccount::findOrFail($id);
        $rentAccount->update(['status' => 'terminated']);

        $rentAccount->rentCycles()
            ->latest()
            ->first()
            ?->update([
                'status' => 'terminated',
                'end_date' => now()
            ]);

        return redirect()->route('rent.active')->with('success', 'Rent account deactivated successfully.');
    }

    public function rentHistory(Request $request, $id)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions'); 
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_rent')))) {
            return redirect()->back()->with('error', 'Unauthorized access to rent history.');
        }        
        $referrer = $request->headers->get('referer');

        $rent_history = RentCycle::with([
            'Apartment:id,tenancy_type,pro_sco_code,property_ref,address,post_code,unique_code',
            'Tenant:id,full_name,occupant_email,date_of_birth'
        ])
        ->select('id', 'tenant_id', 'apartment_id', 'unit_number', 'start_date', 'end_date', 'status',
            'account_type', 'duration_months', 'rent_fee', 'payment_made', 'payment_method',
            'escalation_policy', 'created_by')
        ->where('rent_account_id', $id)
        ->get();

        $rent_history->title = 'Active';

        return view('layouts.rent.account_history', compact('rent_history', 'referrer'));
    }

    public function InactiveHistory(Request $request, $id)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions'); 
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_rent')))) {
            return redirect()->back()->with('error', 'Unauthorized access to rent history.');
        }
        $referrer = $request->headers->get('referer');

        $rent_history = RentCycle::with([
            'Apartment:id,tenancy_type,pro_sco_code,property_ref,ownership,admin_unit,address,post_code,unique_code',
            'Tenant:id,full_name,occupant_email,date_of_birth'
        ])
        ->select('id', 'tenant_id', 'apartment_id', 'unit_number', 'start_date', 'end_date', 'status',
            'account_type', 'duration_months', 'rent_fee', 'payment_made', 'payment_method',
            'escalation_policy', 'created_by')
        ->where('rent_account_id', $id)
        ->get();

        $rent_history->title = 'Inactive';

        return view('layouts.rent.account_history', compact('rent_history', 'referrer'));
    }

    protected function createBooking($validated, $fee)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_booking')))) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to booking creation.'
            ], 403);
        }
        $apartment = ApartmentIdentity::select('id', 'shelter_id')
            ->findOrFail($validated['apartment_id']);

        return BookingModel::create([
            'shelter_id' => $apartment->shelter_id,
            'payment_time_id' => null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'apartment_id' => $validated['apartment_id'],
            'booked_by_user_id' => Auth::id(),
            'tenant_id' => $validated['tenant_id'],
            'booked_by_user_type' => Auth::user()->user_type, // Assuming this is a string to indicate user type (e.g., admin, tenant)
            'updated_by_user_id' => Auth::id(),
            'fee' => $fee,
        ]);
    }

    public function cancelBooking($id)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions'); 
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_booking')))) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to booking cancellation.'
            ], 403);
        }
        $booking = BookingModel::find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found.',
            ], 404);
        }

        $booking->update(['is_cancelled' => true]);

        return true;
    }



    public function createCycle(Request $request){
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_rent')))) {
            return redirect()->back()->with('error', 'Unauthorized access to rent cycle creation.');
        }
        $referrer = $request->headers->get('referer');
  $rent_accounts = RentAccount::with([
        'rentCycles:id,rent_account_id,apartment_id,tenant_id,rent_fee,payment_method,payment_made,start_date,end_date',
        'apartment:id,tenancy_type,pro_sco_code,property_ref,ownership,admin_unit,address,post_code,unique_code',
        'tenant:id,first_name,last_name,occupant_email,date_of_birth'
    ])
    ->select('id', 'tenant_id', 'apartment_id', 'unit_number', 'start_date', 'account_type', 'status')
    ->where('status', 'active')
    ->has('tenant')
    ->orderBy('id', 'desc')
    ->get();

 $rent_accounts->url = 'rent.cycle';
 

        return view('layouts.rent.account_cycle', compact('rent_accounts','referrer'));
        
    }
//     public function Renew(Request $request)
// {
//     $validated = $request->validate([
//         'tenant_id'          => ['required', 'exists:tenants,id'],
//         'apartment_id'       => ['required', 'exists:apartment_identities,id'],
//         'start_date'         => ['required', 'date'],
//         'end_date'           => ['required', 'date', 'after_or_equal:start_date'],
//         'rent_fee'           => ['required', 'numeric', 'min:0'],
//         'rent_account_id'    => ['required', 'numeric', 'min:0'],
//         'apartment_address'  => ['required', 'string'],
//         'escalation_policy'  => ['required', 'string'],
//         'payment_method'     => ['required', 'string'],
//         'payment_made'=>['required','string'],
//         'account_type'=>['required', 'string'],
//     ]);

//     $validated['duration_months'] = Carbon::parse($validated['start_date'])
//         ->floatDiffInMonths(Carbon::parse($validated['end_date']));

//     $validated['created_by'] = Auth::id();
//     $validated['unit_number'] = ApartmentIdentity::where('id', $validated['apartment_id'])->value('unit_number');


//     // Check for conflicting bookings (you need to define the logic for this)
//     if ($this->checkConflict($validated['apartment_id'], $validated['start_date'], $validated['end_date'])) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Apartment rented within the specified time. Check the expiration time and book after it'
//         ], 400);
//     }

//     // Create booking (assumes createBooking method exists and returns BookingModel instance)
//     $booking = $this->createBooking($validated, $validated['rent_fee']);
//     $validated['booking_models_id'] = $booking->id;

//     RentCycle::create($validated);

//     return response()->json([
//         'success' => true,
//         'message' => 'Apartment renewed successfully.'
//     ], 201);
// }


public function Renew(Request $request)
{
    $validated = $request->validate([
        'tenant_id'          => ['required', 'exists:tenants,id'],
        'apartment_id'       => ['required', 'exists:apartment_identities,id'],
        'start_date'         => ['required', 'date'],
        'end_date'           => ['required', 'date', 'after_or_equal:start_date'],
        'rent_fee'           => ['required', 'numeric', 'min:0'],
        'rent_account_id'    => ['required', 'numeric', 'min:0'],
        'apartment_address'  => ['required', 'string'],
        'escalation_policy'  => ['required', 'string'],
        'payment_method'     => ['required', 'string'],
        'payment_made'       => ['required', 'string'],
        'account_type'       => ['required', 'string'],
    ]);

    $validated['duration_months'] = Carbon::parse($validated['start_date'])
        ->floatDiffInMonths(Carbon::parse($validated['end_date']));

    $validated['created_by'] = Auth::id();
    $validated['unit_number'] = ApartmentIdentity::where('id', $validated['apartment_id'])->value('unit_number');

    if ($this->checkConflict($validated['apartment_id'], $validated['start_date'], $validated['end_date'])) {
        return response()->json([
            'success' => false,
            'message' => 'Apartment rented within the specified time. Check the expiration time and book after it'
        ], 400);
    }

    $booking = $this->createBooking($validated, $validated['rent_fee']);
    $validated['booking_models_id'] = $booking->id;

    $rentCycle = RentCycle::create($validated);
// Eager load the 'apartment' relationship after creation
  $rentCycle->load('apartment:id,address');


    // Send email to tenant
    try {
        $tenant = Tenant::findOrFail($validated['tenant_id']);
        Mail::to($tenant->occupant_email)->send(new RentRenewed($tenant, $rentCycle));
    } catch (\Exception $e) {
        \Log::error('Rent renewal email failed: ' . $e->getMessage());
    }

    return response()->json([
        'success' => true,
        'message' => 'Apartment renewed successfully.'
    ], 201);
}


    private function checkConflict($apartment_id,$start_date,$end_date){

        $status = RentCycle::where('start_date', '>=', $start_date)->where('end_date','=<',$end_date)->exists();
        if($status){
            return true;
        }
        return false;
    }
}
