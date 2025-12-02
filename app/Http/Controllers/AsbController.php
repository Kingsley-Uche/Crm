<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AsbModel;
use App\Models\BlockModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
class AsbController extends Controller
{
    // Show all ASB records
    public function index()
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'read_asb')))) {
            return redirect()->back()->with('error', 'Unauthorized access to ASB records.');
        }
        $asb = AsbModel::with('block')->get();
        return view('layouts.asb.index', compact('asb'));
    }

    // Show single ASB record 
    public function show($id)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_asb')))) {
            return redirect()->back()->with('error', 'Unauthorized access to ASB records.');
        }
        $asb = AsbModel::with('block')->findOrFail($id);
        return view('layouts.asb.show', compact('asb'));
    }

    // Show form to create new ASB record
    public function LoadCreate()
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_asb')))) {
            return redirect()->back()->with('error', 'Unauthorized access to create ASB records.');
        }   
        $blocks = json_encode(
            BlockModel::with(['apartments:id,block_models_id,address,unit_number'])
                ->select('id', 'name')
                ->get()
        );

        $fields = [
            ['label' => 'Received Date', 'name' => 'received_date', 'type' => 'date'],
            ['label' => 'Progress', 'name' => 'progress', 'type' => 'select', 'options' => ['In Progress', 'Not Started', 'Completed']],
            ['label' => 'Status', 'name' => 'status', 'type' => 'text'],
            ['label' => 'Deadline Timeframe', 'name' => 'deadline_timeframe', 'type' => 'text'],
            ['label' => 'Issue', 'name' => 'issue', 'type' => 'text'],
            ['label' => 'Appointment Timeframe', 'name' => 'appointment_timeframe', 'type' => 'text'],
            ['label' => 'Description', 'name' => 'description', 'type' => 'textarea'],
            ['label' => 'Action Timeline', 'name' => 'action_timeline', 'type' => 'text'],
            ['label' => 'Assigned To', 'name' => 'assigned_to', 'type' => 'text'],
            ['label' => 'Due Date', 'name' => 'due_date', 'type' => 'date'],
            ['label' => 'Appointment', 'name' => 'appointment', 'type' => 'date'],
            ['label' => 'Completion Date', 'name' => 'completion_date', 'type' => 'date'],
        ];

        return view('layouts.asb.create', compact('blocks', 'fields'));
    }
    
public function store(Request $request)
{
    $user = Session::get('user');
    $permissions = Session::get('permissions');
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'create_asb')))) {
        return redirect()->back()->with('error', 'Unauthorized access to create ASB records.');
    }
    $data = $request->validate([
        'block_id' => 'required|integer|exists:block_models,id',
        'apartment_id' => 'required|integer|exists:apartment_identities,id',
        'unit_number' => 'required|string|max:255',
        'crime_reference' => 'nullable|string|max:255',
        'received_date' => 'nullable|date',
        'progress' => 'nullable|string|in:In Progress,Not Started,Completed',
        'status' => 'nullable|string|max:255',
        'deadline_timeframe' => 'nullable|string|max:255',
        'issue' => 'nullable|string|max:255',
        'appointment_timeframe' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'action_timeline' => 'nullable|string|max:255',
        'assigned_to' => 'nullable|string|max:255',
        'due_date' => 'nullable|date',
        'appointment' => 'nullable|date',
        'completion_date' => 'nullable|date',
        'reporter_email' => 'nullable|email|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        'document' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
        'audio' => 'nullable|mimetypes:audio/mpeg,audio/mp4,audio/wav|max:10240',
        'video' => 'nullable|mimetypes:video/mp4,video/quicktime|max:51200',
    ]);
 
    // Generate unique reference
    $data['ref'] = AsbModel::generateRef();

    // Handle media uploads
    foreach (['image', 'document', 'audio', 'video'] as $type) {
        if ($request->hasFile($type)) {
            $data[$type] = $this->upload_media($request->file($type), $data['ref'], $type);
        }
    }

    AsbModel::create($data);

    return redirect()->route('asb.index')->with('success', 'ASB report created successfully.');
}

    public function edit($id)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_asb')))) {
            return redirect()->back()->with('error', 'Unauthorized access to edit ASB records.');
        }   
        $asb = AsbModel::findOrFail($id);
        $blocks = json_encode(
            BlockModel::with(['apartments:id,block_models_id,address,unit_number'])
                ->select('id', 'name')
                ->get()
        );

        $fields = [
            ['label' => 'Received Date', 'name' => 'received_date', 'type' => 'date'],
            ['label' => 'Progress', 'name' => 'progress', 'type' => 'select', 'options' => ['In Progress', 'Not Started', 'Completed']],
            ['label' => 'Status', 'name' => 'status', 'type' => 'text'],
            ['label' => 'Repair Type', 'name' => 'repair_type', 'type' => 'text'],
            ['label' => 'Deadline Timeframe', 'name' => 'deadline_timeframe', 'type' => 'text'],
            ['label' => 'Issue', 'name' => 'issue', 'type' => 'text'],
            ['label' => 'Appointment Timeframe', 'name' => 'appointment_timeframe', 'type' => 'text'],
            ['label' => 'Description', 'name' => 'description', 'type' => 'textarea'],
            ['label' => 'Action Timeline', 'name' => 'action_timeline', 'type' => 'text'],
            ['label' => 'Assigned To', 'name' => 'assigned_to', 'type' => 'text'],
            ['label' => 'Reference', 'name' => 'ref', 'type' => 'text'],
            ['label' => 'Due Date', 'name' => 'due_date', 'type' => 'date'],
            ['label' => 'Appointment', 'name' => 'appointment', 'type' => 'date'],
            ['label' => 'Completion Date', 'name' => 'completion_date', 'type' => 'date'],
            ['label' => 'Image', 'name' => 'image', 'type' => 'file'],
            ['label' => 'Document', 'name' => 'document', 'type' => 'file'],
            ['label' => 'Audio', 'name' => 'audio', 'type' => 'file'],
            ['label' => 'Video', 'name' => 'video', 'type' => 'file'],
        ];

        return view('layouts.asb.edit', compact('asb', 'blocks', 'fields'));
    }

    // Update ASB record
public function update(Request $request, $id)
{
    $user = Session::get('user');
    $permissions = Session::get('permissions');
    if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'update_asb')))) {
        return redirect()->back()->with('error', 'Unauthorized access to update ASB records.');
    }
    $asb = AsbModel::findOrFail($id);

    $data = $request->validate([
        'block_id' => 'required|integer|exists:block_models,id',
        'apartment_id' => 'required|integer|exists:apartment_identities,id',
        'unit_number' => 'required|string|max:255',
        'crime_reference' => 'required|string|max:255',
        'received_date' => 'nullable|date',
        'progress' => 'nullable|string|in:In Progress,Not Started,Completed',
        'status' => 'nullable|string|max:255',
        'repair_type' => 'nullable|string|max:255',
        'deadline_timeframe' => 'nullable|string|max:255',
        'issue' => 'nullable|string|max:255',
        'appointment_timeframe' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'action_timeline' => 'nullable|string|max:255',
        'assigned_to' => 'nullable|string|max:255',
        'ref' => 'required|string|max:255|unique:asb,ref,' . $id,
        'due_date' => 'nullable|date',
        'appointment' => 'nullable|date',
        'completion_date' => 'nullable|date',
        'reporter_email' => 'nullable|email|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        'document' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
        'audio' => 'nullable|mimetypes:audio/mpeg,audio/mp4,audio/wav|max:10240',
        'video' => 'nullable|mimetypes:video/mp4,video/quicktime|max:51200',
    ]);

    $fileTypes = ['image', 'document', 'audio', 'video'];
    $disk = config('filesystems.asb_disk', 'public');

    foreach ($fileTypes as $type) {
        if ($request->hasFile($type)) {
            // Delete old file
            if ($asb->$type && Storage::disk($disk)->exists($asb->$type)) {
                Storage::disk($disk)->delete($asb->$type);
            }

            // Upload new file
            $data[$type] = $this->upload_media($request->file($type), $data['ref'], $type);
        } else {
            // Retain existing file path
            $data[$type] = $asb->$type;
        }
    }

    $asb->update($data);

    return redirect()->route('asb.index')->with('success', 'ASB updated successfully.');
}


    // Delete ASB record
    public function destroy($id)
    {
        $user = Session::get('user');
        $permissions = Session::get('permissions');
        if (!$user || (!$user->system_admin && (!$permissions || !$permissions->contains('slug', 'delete_asb')))) {
            return redirect()->back()->with('error', 'Unauthorized access to delete ASB records.');
        }   
        $asb = AsbModel::findOrFail($id);
        $fileTypes = ['image', 'document', 'audio', 'video'];
        $disk = config('filesystems.asb_disk', 'public');

        foreach ($fileTypes as $type) {
            if ($asb->$type && Storage::disk($disk)->exists($asb->$type)) {
                Storage::disk($disk)->delete($asb->$type);
            }
        }

        $asb->delete();
        $asb->delete();
return redirect()->route('asb.index')->with('success', 'ASB deleted successfully.');

       
    }

    // Handle media uploads
    private function upload_media($file, $ref, $type)
    {
        
        $disk = config('filesystems.asb_disk', 'public');

        // Create directory if it doesn't exist
        $directory = "asb_files/$type";
        Storage::disk($disk)->makeDirectory($directory);

        // Generate unique filename
        $timestamp = now()->format('Ymd_His');
        $extension = $file->getClientOriginalExtension();
        $filename = "{$ref}_{$timestamp}.{$extension}";

        // Store the file and get the relative path
        $relativePath = $file->storeAs($directory, $filename, $disk);

        // Return the relative file path (e.g., asb_files/image/ref_20250504_123456.jpg)
        return $relativePath;
    }
}