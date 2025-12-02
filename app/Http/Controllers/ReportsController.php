<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApartmentIdentity;
use App\Models\RentAccount;
use App\Models\RentCycle;
use App\Models\Repairs;
use App\Models\BlockModel;
use App\Models\PestModel;
use App\Models\VoidsModel;
use App\Models\TenantModel as Tenant;
use Illuminate\Support\Facades\Auth;
use App\Models\ComplaintModel as Complaint;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class ReportsController extends Controller
{
    //
public function RentReport(Request $request)
{
    $user = Session::get('user');
    $permissions = Session::get('permissions');

    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_reports')))) {
        return redirect()->back()->with('error', 'Unauthorized access to rent reports.');
    }

    $validated = $request->validate([
        'start_date' => ['required', 'date'],
        'end_date' => ['required', 'date', 'after_or_equal:start_date'],
    ]);

    $rent_accounts = RentAccount::with([
        'rentCycles:id,rent_account_id,apartment_id,tenant_id,rent_fee,payment_method,payment_made,start_date,end_date',
        'apartment:id,tenancy_type,pro_sco_code,property_ref,ownership,admin_unit,address,post_code,unique_code',
        'tenant:id,first_name,last_name,occupant_email,mobile_number,home_number'
    ])
    ->select('id', 'tenant_id', 'apartment_id', 'unit_number', 'start_date', 'account_type', 'status')
    ->where('status', 'active')
    ->orderBy('id', 'desc')
    ->get();

    $flatReport = [];

    foreach ($rent_accounts as $account) {
        $apartment = $account->apartment;
        $tenant = $account->tenant;

        foreach ($account->rentCycles as $cycle) {
            $flatReport[] = [
                // RentCycle info
                'cycle_start_date' => $cycle->start_date,
                'cycle_end_date' => $cycle->end_date,
                'rent_fee' => $cycle->rent_fee,
                'payment_method' => $cycle->payment_method,
                'payment_made' => $cycle->payment_made,

                // Apartment info
                'address' => $apartment->address ?? '',
                'pro_sco_code' => $apartment->pro_sco_code ?? '',
                'tenancy_type' => $apartment->tenancy_type ?? '',
                'property_ref' => $apartment->property_ref ?? '',
                'ownership' => $apartment->ownership ?? '',
                'admin_unit' => $apartment->admin_unit ?? '',
                'post_code' => $apartment->post_code ?? '',
                'unique_code' => $apartment->unique_code ?? '',

                // Tenant info
                'first_name' => $tenant->first_name ?? '',
                'last_name' => $tenant->last_name ?? '',
                'occupant_email' => $tenant->occupant_email ?? '',
                'mobile_number' => $tenant->mobile_number ?? '',
                'home_number' => $tenant->home_number ?? '',
            ];
        }
    }

    return view('layouts.report.rent.rent_report', compact('flatReport'));     
    }
    public function Rent(){
            $user = Session::get('user');
            $permissions = Session::get('permissions');

            if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_reports')))) {
                return redirect()->back()->with('error', 'Unauthorized access to rent reports.');
            }

         return view('layouts.report.rent.index');
    }

 public function MaintenanceReport(Request $request)
{
    $user = Session::get('user');
    $permissions = Session::get('permissions');
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_reports')))) {
        return redirect()->back()->with('error', 'Unauthorized access to maintenance reports.');
    }
    $validated = $request->validate([
        'start_date' => ['required', 'date'],
        'end_date'   => ['required', 'date', 'after_or_equal:start_date'],
    ]);
$repairs = Repairs::with(['block:id,name'])
    ->whereBetween('received_date', [$validated['start_date'], $validated['end_date']])
    ->orderBy('received_date', 'desc')
    ->select(
        'id', 'unit_number', 'progress', 'status', 'repair_type', 'deadline_timeframe',
        'issue', 'appointment_timeframe', 'description', 'action_timeline', 'assigned_to',
        'ref', 'due_date', 'appointment', 'completion_date','block_id'
    )
    ->get();
    return view('layouts.report.maintenance.maintenance_report', compact('repairs'));
}

    public function Maintenance(){
         return view('layouts.report.maintenance.index');  
    }      
    
public function PestControlReport(Request $request)
{

     $user = Session::get('user');
    $permissions = Session::get('permissions');
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_reports')))) {
        return redirect()->back()->with('error', 'Unauthorized access to pest control reports.');
    }
    $validated = $request->validate([
        'start_date' => ['required', 'date'],
        'end_date' => ['required', 'date', 'after_or_equal:start_date'],
    ]);

    $pests = PestModel::with('block:id,name','apartment:id,address')
        ->whereBetween('received_date', [$validated['start_date'], $validated['end_date']])
        ->orderBy('received_date', 'desc')
        ->get([
            'id',
            'block_id',
            'apartment_id',
            'issue_type',
            'description',
            'status',
            'ref',
            'received_date',
            'progress',
            'deadline_timeframe',
            'appointment_timeframe',
            'action_timeline',
            'assigned_to',
            'due_date',
            'appointment',
            'completion_date',
            'pest_control_fee',
        ]);

    return view('layouts.report.pest_control.pest_control_report', compact('pests'));
}

    public function PestControl(){
    $user = Session::get('user');
    $permissions = Session::get('permissions');
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_reports')))) {
        return redirect()->back()->with('error', 'Unauthorized access to pest control reports.');
    }
    return view('layouts.report.pest_control.index');
}

public function VoidsReport(Request $request)
{
    $user = Session::get('user');
    $permissions = Session::get('permissions');
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_reports')))) {
        return redirect()->back()->with('error', 'Unauthorized access to voids reports.');
    }
    // Validate the request data
    $validated = $request->validate([
        'start_date' => ['required', 'date'],
        'end_date' => ['required', 'date', 'after_or_equal:start_date'],
    ]);

    $voids = VoidsModel::whereBetween('termination_date', [$validated['start_date'], $validated['end_date']])
        ->orderBy('termination_date', 'desc')
        ->get();
  return view('layouts.report.voids.voids_report', compact('voids'));
}

    public function Voids(){
    $user = Session::get('user');
    $permissions = Session::get('permissions');
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_reports')))) {
        return redirect()->back()->with('error', 'Unauthorized access to voids reports.');
    }
         return view('layouts.report.voids.index');
        
    }
    
    public function ComplaintsReport(Request $request)
{
    
     $user = Session::get('user');
    $permissions = Session::get('permissions');
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_reports')))) {
        return redirect()->back()->with('error', 'Unauthorized access to voids reports.');
    }
    // Validate the incoming request
    $validated = $request->validate([
        'start_date' => ['required', 'date'],
        'end_date' => ['required', 'date', 'after_or_equal:start_date'],
    ]);

    // Parse and format the dates
    $startDate = Carbon::parse($validated['start_date'])->startOfDay();
    $endDate = Carbon::parse($validated['end_date'])->endOfDay();

    // Retrieve complaints within the date range
    $complaints = Complaint::whereBetween('created_at', [$startDate, $endDate])->get();

    // Check if complaints exist in range
    if ($complaints->isEmpty()) {
        return redirect()->back()->with('error', 'No complaints found for the selected date range.');
    }

    // You can pass the data to a view, export, or generate PDF as needed
    return view('layouts.report.complaints.complaints_report',compact('complaints'));


}
public function complaints(){
    $user = Session::get('user');
    $permissions = Session::get('permissions');
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_reports')))) {
        return redirect()->back()->with('error', 'Unauthorized access to complaints reports.');
    }
         return view('layouts.report.complaints.index');
        
    }
}
