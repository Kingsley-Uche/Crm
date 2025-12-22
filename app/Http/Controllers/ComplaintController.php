<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ComplaintModel as Complaint;
use App\Models\BlockModel;
use App\Models\ApartmentIdentity;
use App\Models\TenantModel as Tenant;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class ComplaintController extends Controller
{
    public function index()
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');

        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_complaints')))) {
            return redirect()->back()->with('error', 'Unauthorized access to complaints.');
        }

        $complaints = Complaint::with('block')->get();
        return view('layouts.complaints.index', compact('complaints'));
    }

    public function create()
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');

        if (
            !$user ||
            (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_complaints')))
        ) {
            return redirect()->back()->with('error', 'Unauthorized access to create complaint.');
        }

        $blocks = json_encode(
            BlockModel::with(['apartments:id,block_models_id,address,unit_number'])
                ->select('id', 'name')
                ->get()
        );

        $tenants = Tenant::select('id', 'full_name')->get();

        $fields = [
            
           
            ['label' => 'Subject', 'name' => 'subject', 'type' => 'text', 'required' => true],
            ['label' => 'Complainant Email Address', 'name' => 'email', 'type' => 'email'],
            ['label' => 'Complainant Phone Number', 'name' => 'phone', 'type' => 'text'],
             
             ['label' => 'Tenant', 'name' => 'tenant_id', 'type' => 'select', 'options' => $tenants->map(function($t) {
                return ['value' => $t->id, 'label' => "{$t->full_name}"];
            })->toArray(), 'required' => true],
             ['label' => 'Received Date', 'name' => 'received_date', 'type' => 'date', 'required' => true],
              ['label' => 'Resolution Date', 'name' => 'resolved_date', 'type' => 'date'],
              ['label' => 'Action Taken', 'name' => 'action_taken', 'type' => 'textarea'],
                ['label' => 'Status', 'name' => 'status', 'type' => 'select', 'options' => [
                ['value' => 'pending', 'label' => 'Pending'],
                ['value' => 'in_progress', 'label' => 'In Progress'],
                ['value' => 'completed', 'label' => 'Completed'],
            ]],
           
           ['label' => 'Assigned To', 'name' => 'assigned_to', 'type' => 'text'],
            ['label' => 'Description', 'name' => 'description', 'type' => 'textarea', 'required' => true],
           
           
           
           
        ];

        return view('layouts.complaints.create', compact('blocks', 'tenants', 'fields'));
    }

    public function store(Request $request)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');

        if (
            !$user ||
            (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_complaints')))
        ) {
            return redirect()->back()->with('error', 'Unauthorized access to create complaint.');
        }

        $request->validate([
            'block_id' => 'required|exists:block_models,id',
            'apartment_id' => 'required|exists:apartment_identities,id',
            'unit_number' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'status' => 'nullable|in:pending,in_progress,completed',
            'assigned_to' => 'nullable|string|max:255',
            'tenant_id' => 'required|exists:tenants,id',
            'phone' => 'nullable|regex:/^[0-9\s\-\+\(\)]*$/|min:7|max:20',
            'email' => 'nullable|email|max:255',
            'received_date' => 'required|date',
            'resolved_date' => 'nullable|date|after_or_equal:received_date',
            'action_taken' => 'nullable|string|max:400',
        ]);

        Complaint::create([
            'block_id' => $request->block_id,
            'apartment_id' => $request->apartment_id,
            'unit_number' => trim($request->unit_number),
            'subject' => trim($request->subject),
            'description' => trim($request->description),
            'status' => $request->status ?? 'pending',
            'assigned_to' => $request->assigned_to ? trim($request->assigned_to) : null,
            'tenant_id' => $request->tenant_id,
            'phone' => $request->phone ? trim($request->phone) : null,
            'email' => $request->email ? trim($request->email) : null,
            'created_by_admin_id' => $user->id ?? null,
            'received_date' => $request->received_date,
            'resolved_date' => $request->resolved_date,
            'action_taken' => $request->action_taken ? trim($request->action_taken) : null,
        ]);

        return redirect()->route('complaints.index')->with('success', 'Complaint submitted successfully.');
    }

    public function edit($id)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');

        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_complaints')))) {
            return redirect()->back()->with('error', 'Unauthorized access to edit complaint.');
        }

        $complaint = Complaint::findOrFail($id);
        $blocks = json_encode(BlockModel::with(['apartments:id,block_models_id,address,unit_number'])->select('id', 'name')->get());
        $tenants = Tenant::select('id', 'full_name')->get();

       $fields = [
            ['label' => 'Subject', 'name' => 'subject', 'type' => 'text', 'required' => true],
            ['label' => 'Description', 'name' => 'description', 'type' => 'textarea', 'required' => true],
            ['label' => 'Assigned To', 'name' => 'assigned_to', 'type' => 'text'],
            ['label' => 'Received Date', 'name' => 'received_date', 'type' => 'date', 'required' => true],
            ['label' => 'Action Taken', 'name' => 'action_taken', 'type' => 'textarea'],
            ['label' => 'Status', 'name' => 'status', 'type' => 'select', 'options' => [
                ['value' => 'pending', 'label' => 'Pending'],
                ['value' => 'in_progress', 'label' => 'In Progress'],
                ['value' => 'completed', 'label' => 'Completed'],
            ]],
            ['label' => 'Resolution Date', 'name' => 'resolved_date', 'type' => 'date'],
            ['label' => 'Tenant', 'name' => 'tenant_id', 'type' => 'select', 'options' => $tenants->map(function($t) {
                return ['value' => $t->id, 'label' => "{$t->full_name}"];
            })->toArray(), 'required' => true],
            ['label' => 'Complainant Email Address', 'name' => 'email', 'type' => 'email'],
            ['label' => 'Complainant Phone Number', 'name' => 'phone', 'type' => 'text'],
        ];


        return view('layouts.complaints.edit', compact('complaint', 'blocks', 'tenants', 'fields'));
    }

    public function update(Request $request, $id)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');

        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_complaints')))) {
            return redirect()->back()->with('error', 'Unauthorized access to update complaint.');
        }

        $request->validate([
            'block_id' => 'required|exists:block_models,id',
            'apartment_id' => 'required|exists:apartment_identities,id',
            'unit_number' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'status' => 'nullable|in:pending,in_progress,completed',
            'assigned_to' => 'nullable|string|max:255',
            'tenant_id' => 'required|exists:tenants,id',
            'phone' => 'nullable|regex:/^[0-9\s\-\+\(\)]*$/|min:7|max:20',
            'email' => 'nullable|email|max:255',
            'received_date' => 'required|date',
            'resolved_date' => 'nullable|date|after_or_equal:received_date',
            'action_taken' => 'nullable|string|max:400',
        ]);

        $complaint = Complaint::findOrFail($id);
        $complaint->update([
            'block_id' => $request->block_id,
            'apartment_id' => $request->apartment_id,
            'unit_number' => $request->unit_number,
            'subject' => trim($request->subject),
            'description' => trim($request->description),
            'status' => $request->status ?? 'pending',
            'assigned_to' => trim($request->assigned_to ?? ''),
            'tenant_id' => $request->tenant_id,
            'phone' => trim($request->phone ?? ''),
            'email' => trim($request->email ?? ''),
            'received_date' => $request->received_date,
            'resolved_date' => $request->resolved_date,
            'action_taken' => trim($request->action_taken ?? ''),
        ]);

        return redirect()->route('complaints.index')->with('success', 'Complaint updated successfully.');
    }

    public function destroy($id)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');

        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'delete_complaints')))) {
            return redirect()->back()->with('error', 'Unauthorized access to delete complaint.');
        }

        $complaint = Complaint::findOrFail($id);
        $complaint->delete();

        return redirect()->route('complaints.index')->with('success', 'Complaint deleted successfully.');
    }

    public function generateReport(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $startDate = Carbon::parse($validated['start_date'])->startOfDay();
        $endDate = Carbon::parse($validated['end_date'])->endOfDay();

        $complaints = Complaint::whereBetween('created_at', [$startDate, $endDate])->get();

        if ($complaints->isEmpty()) {
            return redirect()->back()->with('error', 'No complaints found for the selected date range.');
        }

        return view('layouts.reports.complaints_report', compact('complaints'));
    }
}
