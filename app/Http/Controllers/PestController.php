<?php

namespace App\Http\Controllers;

use App\Models\PestModel;
use App\Models\BlockModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Session;
class PestController extends Controller
{
    /**
     * Display a listing of pest reports.
     */
  public function index()
{
    $user = auth()->user();
    $permissions = session('permissions');  
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_pest_control')))) {
        return redirect()->back()->with('error', 'Unauthorized access to pest control.');
    }
    $pests = PestModel::with('block:id,name')->get([
        'id',
        'block_id',
        'apartment_id',
        'issue_type',
        'description',
        'status',
        'ref',
        'image',
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

    return view('layouts.pest_control.index', compact('pests'));
}


    /**
     * Display a specific pest report.
     */
 public function show($pest_id)
    {
        $user = auth()->user();
        $permissions = session('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_pest_control')))) {
            return redirect()->back()->with('error', 'Unauthorized access to pest control.');
        }
        $pest = PestModel::with(['block:id,name', 'apartment:id,block_models_id,unit_number'])->findOrFail($pest_id);
        $blocks = BlockModel::with(['apartments:id,block_models_id,address,unit_number'])
            ->select('id', 'name')
            ->get()
            ->toJson();
       $fields = $this->getFormFields();
        return view('layouts.pest_control.edit', compact('pest', 'blocks', 'fields'));
    }
    /**
     * Show the form for creating a new pest report.
     */
    public function create()
    {
        $user = auth()->user();
        $permissions = session('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_pest_control')))) {
            return redirect()->back()->with('error', 'Unauthorized access to create pest control.');
        }
        $blocks = BlockModel::with(['apartments:id,block_models_id,address,unit_number'])
            ->select('id', 'name')
            ->get()
            ->toJson();

        $fields = $this->getFormFields();
        
        return view('layouts.pest_control.create', compact('blocks', 'fields'));
    }

    /**
     * Store a newly created pest report.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $permissions = session('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_pest_control')))) {
            return redirect()->back()->with('error', 'Unauthorized access to create pest control.');
        }   
        $data = $request->validate([
            'block_id' => ['required', 'exists:block_models,id'],
            'apartment_id' => ['required', 'integer', 'exists:apartment_identities,id'],
            'issue_type' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:255'],
            'received_date' => ['nullable', 'date'],
            'progress' => ['nullable', 'in:In Progress,Not Started,Completed'],
            'deadline_timeframe' => ['nullable', 'string', 'max:255'],
            'appointment_timeframe' => ['nullable', 'string', 'max:255'],
            'action_timeline' => ['nullable', 'string', 'max:255'],
            'assigned_to' => ['nullable', 'string', 'max:255'], // UK: Pest control contractor details
            'due_date' => ['nullable', 'date', 'after_or_equal:received_date'],
            'appointment' => ['nullable', 'date', 'after_or_equal:received_date'],
            'completion_date' => ['nullable', 'date', 'after_or_equal:appointment'],
            'pest_control_fee' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $data['ref'] = PestModel::generateRef();

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadMedia($request->file('image'), $data['ref'], 'image');
        }

        PestModel::create($data);

        return redirect()->route('pest_control.index')->with('success', 'Pest report created successfully.');
    }

    /**
     * Show the form for editing a pest report.
     */
    public function edit($id)
    {
        $user = auth()->user();
        $permissions = session('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_pest_control')))) {
            return redirect()->back()->with('error', 'Unauthorized access to edit pest control.');
        }
        $pest = PestModel::findOrFail($id);
        $blocks = BlockModel::with(['apartments:id,block_models_id,address,unit_number'])
            ->select('id', 'name')
            ->get()
            ->toJson();
        $fields = $this->getFormFields();
      

        return view('pest_control.edit', compact('pest', 'blocks', 'fields'));
    }

    /**
     * Update an existing pest report.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $permissions = session('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_pest_control')))) {
            return redirect()->back()->with('error', 'Unauthorized access to update pest control.');
        }
        $pest = PestModel::findOrFail($id);

        $data = $request->validate([
            'block_id' => ['required', 'exists:block_models,id'],
            'apartment_id' => ['required', 'integer', 'exists:apartment_identities,id'],
            'issue_type' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:255'],
            'ref' => ['required', 'string', 'max:255', Rule::unique('pest_models', 'ref')->ignore($id)],
            'received_date' => ['nullable', 'date'],
            'progress' => ['nullable', 'in:In Progress,Not Started,Completed'],
            'deadline_timeframe' => ['nullable', 'string', 'max:255'],
            'appointment_timeframe' => ['nullable', 'string', 'max:255'],
            'action_timeline' => ['nullable', 'string', 'max:255'],
            'assigned_to' => ['nullable', 'string', 'max:255'],
            'due_date' => ['nullable', 'date', 'after_or_equal:received_date'],
            'appointment' => ['nullable', 'date', 'after_or_equal:received_date'],
            'completion_date' => ['nullable', 'date', 'after_or_equal:appointment'],
            'pest_control_fee' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            if ($pest->image) {
                Storage::disk('public')->delete($pest->image);
            }
            $data['image'] = $this->uploadMedia($request->file('image'), $data['ref'], 'image');
        }

        $pest->update($data);

        return redirect()->route('pest_control.index')->with('success', 'Pest report updated successfully.');
    }

    /**
     * Delete a pest report.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $permissions = session('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'delete_pest_control')))) {
            return redirect()->back()->with('error', 'Unauthorized access to delete pest control.');
        }
        $pest = PestModel::findOrFail($id);

        if ($pest->image) {
            Storage::disk('public')->delete($pest->image);
        }

        $pest->delete();

        return response()->json(['message' => 'Pest report deleted successfully.']);
    }

    /**
     * Upload media files for pest reports.
     */
    private function uploadMedia($file, $ref, $type)
    {
        $directory = "pest_files/{$type}";
        $filename = "{$ref}_" . now()->format('Ymd_His') . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($directory, $filename, 'public');

        return $path;
    }

    /**
     * Define form fields for create/edit views.
     */
    private function getFormFields()
    {
        return [
            ['label' => 'Received Date', 'name' => 'received_date', 'type' => 'date'],
            ['label' => 'Progress', 'name' => 'progress', 'type' => 'select', 'options' => ['In Progress', 'Not Started', 'Completed']],
            ['label' => 'Status', 'name' => 'status', 'type' => 'text'],
            ['label' => 'Deadline Timeframe', 'name' => 'deadline_timeframe', 'type' => 'text'],
            ['label' => 'Issue Type', 'name' => 'issue_type', 'type' => 'text'],
            ['label' => 'Appointment Timeframe', 'name' => 'appointment_timeframe', 'type' => 'text'],
            ['label' => 'Description', 'name' => 'description', 'type' => 'textarea'],
            ['label' => 'Action Timeline', 'name' => 'action_timeline', 'type' => 'text'],
            ['label' => 'Assigned To', 'name' => 'assigned_to', 'type' => 'text'],
            ['label' => 'Due Date', 'name' => 'due_date', 'type' => 'date'],
            ['label' => 'Appointment', 'name' => 'appointment', 'type' => 'date'],
            ['label' => 'Completion Date', 'name' => 'completion_date', 'type' => 'date'],
            ['label' => 'Fee', 'name' => 'pest_control_fee', 'type' => 'number'],
        ];
    }
}