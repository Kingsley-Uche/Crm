<?php

namespace App\Http\Controllers;

use App\Models\FobModel;
use App\Models\TenantModel as Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
class FobController extends Controller
{
    // Show all fobs
    public function index()
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_fob')))) {
            return redirect()->back()->with('error', 'Unauthorized access to fobs.');
        }
        $fobs = FobModel::with('tenant:id,full_name')->get();
        return view('layouts.fob.index', compact('fobs'));
    }

    // Show form to create new fob
    public function create()
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions'); 
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_fob')))) {
            return redirect()->back()->with('error', 'Unauthorized access to create fobs.');
        }   
        $tenants = Tenant::all();
        return view('layouts.fob.create', compact('tenants'));
    }

    // Store new fob
    public function store(Request $request)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_fob')))) {
            return redirect()->back()->with('error', 'Unauthorized access to create fobs.');
        }
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'fob_uid' => 'required|unique:fobs,fob_uid',
            'make' => 'nullable|string',
            'model' => 'nullable|string',
            'type' => 'required|in:rfid,nfc,ble,smartcard',
            'fob_status' => 'required|in:active,lost,malfunctioning,deactivated',
            'request_reason' => 'nullable|string',
            'request_date' => 'nullable|date',
            'issued_date' => 'nullable|date',
            'fee' => 'nullable|numeric',
        ]);

        FobModel::create($validated);
        return redirect()->route('fobs.index')->with('success', 'FOB created successfully.');
    }

    // Show a single fob
    public function show($id)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_fob')))) {
            return redirect()->back()->with('error', 'Unauthorized access to fobs.');
        }   
        $fob = FobModel::with('tenant')->findOrFail($id);
        return view('layouts.fob.show', compact('fob'));
    }

    // Show form to edit a fob
    public function edit($id)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_fob')))) {
            return redirect()->back()->with('error', 'Unauthorized access to edit fobs.');
        }
        $fob = FobModel::findOrFail($id);
        $tenants = Tenant::all();
        return view('layouts.fob.edit', compact('fob', 'tenants'));
    }

    // Update an existing fob
    public function update(Request $request, $id)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions'); 
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_fob')))) {
            return redirect()->back()->with('error', 'Unauthorized access to update fobs.');
        }
        $fob = FobModel::findOrFail($id);

        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'fob_uid' => 'required|unique:fobs,fob_uid,' . $id,
            'make' => 'nullable|string',
            'model' => 'nullable|string',
            'type' => 'required|in:rfid,nfc,ble,smartcard',
            'fob_status' => 'required|in:active,lost,malfunctioning,deactivated',
            'request_reason' => 'nullable|string',
            'request_date' => 'nullable|date',
            'issued_date' => 'nullable|date',
            'fee' => 'nullable|numeric',
        ]);

        $fob->update($validated);
        return redirect()->route('fobs.index')->with('success', 'FOB updated successfully.');
    }

    // Delete a fob
    public function destroy($id)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'delete_fob')))) {
            return redirect()->back()->with('error', 'Unauthorized access to delete fobs.');
        }
        $fob = FobModel::findOrFail($id);
        $fob->delete();

        return redirect()->route('fobs.index')->with('success', 'FOB deleted successfully.');
    }
}
