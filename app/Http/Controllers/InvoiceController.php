<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\InvoiceModel;
use App\Models\BranchModel;
use App\Models\LocationModel;
use App\Models\ApartmentIdentity;
use App\Models\TenantModel;
use App\Models\PaymentListingModel;
use App\Models\BrandModel;
use Illuminate\Support\Facades\Session;

class InvoiceController extends Controller
{
    /**
     * Display invoice listing.
     */
    public function index()
    {
         $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_invoice')))) {
            return redirect()->back()->with('error', 'Unauthorized access to invoices.');
        }
        $invoices = InvoiceModel::with([
            'tenant',
            'apartment',
            'location',
            'branch'
        ])
        ->latest()
        ->paginate(20);//chunk in 100 andpaginate frontend;

        return view('layouts.invoice.index', compact('invoices'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        
         $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_invoice')))) {
            return redirect()->back()->with('error', 'Unauthorized access to invoices.');
        }
        $branches = BranchModel::orderBy('name')->get();
        $tenants= TenantModel::select('id','full_name','occupant_email', 'mobile_number')->get();
        return view('layouts.invoice.create', compact('branches','tenants'));
    }

    /**
     * Store invoice.
     */
    public function store(Request $request)
    {
        
         $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_invoice')))) {
            return redirect()->back()->with('error', 'Unauthorized access to invoices.');
        }

    ///should come after saving to payment listing
       $request->validate([
    'tenant_id'       => 'required|exists:tenants,id',
    'apartment_id'    => 'required|exists:apartment_identities,id',
    'location_id'     => 'nullable|exists:location_models,id',
    'branch_id'       => 'nullable|exists:branch_models,id',
    'amount'          => 'required|numeric|min:0',
    'paid_amount'     => 'nullable|numeric|min:0',
    'description'     => 'nullable|string',
    'due_date'        => 'nullable|date',

    'items'                => 'required|array|min:1',
    'items.*.name'         => 'required|string|max:255',
    'items.*.qty'          => 'required|numeric|min:1',
    'items.*.unit_charge'  => 'required|numeric|min:0',
    'items.*.amount'       => 'required|numeric|min:0',
]);
    

       DB::beginTransaction();

try {

    $amount = (float) $request->amount;
    $paidAmount = (float) ($request->paid_amount ?? 0);

    $invoice = InvoiceModel::create([
        'invoice_ref'  => $this->generateInvoiceRef(),
        'tenant_id'    => $request->tenant_id,
        'apartment_id' => $request->apartment_id,
        'location_id'  => $request->location_id,
        'branch_id'    => $request->branch_id,
        'amount'       => $amount,
        'paid_amount'  => $paidAmount,
        'balance'      => $amount - $paidAmount,
        'description'  => $request->description,
        'status'       => ($amount - $paidAmount) <= 0
                            ? 'paid'
                            : 'pending',
        'due_date'     => $request->due_date,
        'created_by'   => Auth::id(),
    ]);

    foreach ($request->items as $item) {

        PaymentListingModel::create([
            'invoice_id'   => $invoice->id,
            'name'         => $item['name'],
            'qty'          => $item['qty'],
            'unit_charge'  => $item['unit_charge'],
            'amount'       => $item['amount'],
        'tenant_id'    => $request->tenant_id,
        'apartment_id' => $request->apartment_id,
        'location_id'  => $request->location_id,
        ]);
    }

    DB::commit();

    return redirect()
        ->route('invoice.index')
        ->with('success', 'Invoice created successfully.');

} catch (\Exception $e) {

    DB::rollBack();

    return back()
        ->withInput()
        ->with('error', $e->getMessage());
}
    }

    /**
     * Show invoice details.
     */
    public function show($id)
    {
       $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_invoice')))) {
            return redirect()->back()->with('error', 'Unauthorized access to invoices.');
        }

        $brand_data =BrandModel::select('name', 'brand_color',
        'website_url','contact_email','contact_phone','address', 'logo_url')->first(); 
        
        //allow default if not supplied or leave null;
        $invoice = InvoiceModel::with([
            'tenant',
            'apartment',
            'location',
            'branch',
            'paymentListings'
        ])->findOrFail($id);

        return view('layouts.invoice.view', compact('invoice','brand_data'));
    }

    /**
     * Edit invoice.
     */
    public function edit(int $id)
{
     $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_invoice')))) {
            return redirect()->back()->with('error', 'Unauthorized access to invoices.');
        }

    $invoice = InvoiceModel::with([
        'tenant',
        'apartment',
        'location',
        'branch',
        'paymentListings'
    ])->findOrFail($id);

    return view('layouts.invoice.edit', compact('invoice'));
}
    /**
     * Update invoice.
     */
public function update(Request $request, $id)
{
     $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_invoice')))) {
            return redirect()->back()->with('error', 'Unauthorized access to invoices.');
        }

    $invoice = InvoiceModel::with('paymentListings')->findOrFail($id);

    $request->validate([
        'tenant_id'       => 'required|exists:tenants,id',
        'apartment_id'    => 'required|exists:apartment_identities,id',
        'location_id'     => 'nullable|exists:location_models,id',
        'branch_id'       => 'nullable|exists:branch_models,id',
        'paid_amount'     => 'nullable|numeric|min:0',
        'description'     => 'nullable|string',
        'due_date'        => 'nullable|date',

        'new_items'                  => 'nullable|array',
        'new_items.*.name'           => 'required|string|max:255',
        'new_items.*.qty'            => 'required|numeric|min:1',
        'new_items.*.unit_charge'    => 'required|numeric|min:0',
    ]);

    DB::beginTransaction();

    try {

        /**
         * Existing invoice items total
         */
        $existingTotal = $invoice->paymentListings->sum('amount');

        /**
         * New items total
         */
        $newItemsTotal = 0;

        if ($request->filled('new_items')) {

            foreach ($request->new_items as $item) {

                $newItemsTotal +=
                    ((float) $item['qty']) *
                    ((float) $item['unit_charge']);
            }
        }

        /**
         * Final invoice total
         */
        $amount = $existingTotal + $newItemsTotal;

        $paidAmount = (float) ($request->paid_amount ?? 0);

        $balance = max(0, $amount - $paidAmount);

        $status = $balance <= 0
            ? 'paid'
            : ($paidAmount > 0 ? 'partially_paid' : 'pending');

        /**
         * Update invoice
         */
        $invoice->update([
            'tenant_id'    => $request->tenant_id,
            'apartment_id' => $request->apartment_id,
            'location_id'  => $request->location_id,
            'branch_id'    => $request->branch_id,
            'amount'       => $amount,
            'paid_amount'  => $paidAmount,
            'balance'      => $balance,
            'description'  => $request->description,
            'status'       => $status,
            'due_date'     => $request->due_date,
            'updated_by'   => Auth::id(),
        ]);

        /**
         * Save ONLY newly added items
         */
        if ($request->filled('new_items')) {

            foreach ($request->new_items as $item) {

                PaymentListingModel::create([
                    'invoice_id'   => $invoice->id,
                    'name'         => $item['name'],
                    'qty'          => $item['qty'],
                    'unit_charge'  => $item['unit_charge'],
                    'amount'       => $item['qty'] * $item['unit_charge'],
                    'tenant_id'    => $request->tenant_id,
                    'apartment_id' => $request->apartment_id,
                    'location_id'  => $request->location_id,
                ]);
            }
        }

        DB::commit();

        return redirect()
            ->route('invoice.index')
            ->with('success', 'Invoice updated successfully.');

    } catch (\Throwable $e) {

        DB::rollBack();

        return back()
            ->withInput()
            ->with('error', $e->getMessage());
    }
}

    /**
     * Delete invoice.
     */
    public function destroy($id)
    {
         $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'delete_invoice')))) {
            return redirect()->back()->with('error', 'Unauthorized access to invoices.');
        }

        $invoice = InvoiceModel::findOrFail($id);

        $invoice->delete();

        return redirect()
            ->route('invoice.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    /**
     * AJAX: Get locations by branch.
     */
    public function getLocations($branchId)
    {
        $locations = LocationModel::where('branch_id', $branchId)
            ->orderBy('name')
            ->get([
                'id',
                'name'
            ]);

        return response()->json($locations);
    }

    /**
     * AJAX: Get apartments by location.
     */
    public function getApartments($locationId)
    {
        $apartments = ApartmentIdentity::where('location_models_id', $locationId)
            ->orderBy('unique_code')
            ->get([
                'id',
                'property_ref',
                'unique_code',
                'address'
            ]);

        return response()->json($apartments);
    }

    /**
     * Generate invoice reference.
     */
    private function generateInvoiceRef()
    {
        do {
            $ref = 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (
            InvoiceModel::where('invoice_ref', $ref)->exists()
        );

        return $ref;
    }
    public function getInvoiceSummary(?int $year = null): array
{
    $year = $year ?? now()->year;

    $pendingAmount = InvoiceModel::whereYear('created_at', $year)
        ->where('status', 'pending')
        ->sum('balance');

    $paidAmount = InvoiceModel::whereYear('created_at', $year)
        ->where('status', '!=', 'cancelled')
        ->sum('paid_amount');

    return [
        'year' => $year,
        'pending_amount' => (float) $pendingAmount,
        'paid_amount' => (float) $paidAmount,
    ];
}
}