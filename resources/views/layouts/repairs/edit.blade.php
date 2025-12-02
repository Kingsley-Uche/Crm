@extends('layouts.dashboard.landpage')

@section('content')

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 px-1">Edit Repair</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('maintenance.index') }}">Repairs & Maintenance
 </a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h6 class='text-center text-muted'>{{$repair->unit_number.' '.ucwords( $repair->block->name)}}</h6>
                <form action="{{ route('maintenance.update', ['repair_id' => $repair->id]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        @php
                            $fields = [
                                ['label' => 'Received Date', 'name' => 'received_date', 'type' => 'date', 'value' => $repair->received_date],
                                ['label' => 'Progress', 'name' => 'progress', 'type' => 'text', 'value' => $repair->progress],
                                ['label' => 'Status', 'name' => 'status', 'type' => 'text', 'value' => $repair->status],
                                ['label' => 'Repair Type', 'name' => 'repair_type', 'type' => 'text', 'value' => $repair->repair_type],
                                ['label' => 'Deadline Timeframe', 'name' => 'deadline_timeframe', 'type' => 'text', 'value' => $repair->deadline_timeframe],
                                ['label' => 'Issue', 'name' => 'issue', 'type' => 'text', 'value' => $repair->issue],
                                ['label' => 'Appointment Timeframe', 'name' => 'appointment_timeframe', 'type' => 'text', 'value' => $repair->appointment_timeframe],
                                ['label' => 'Action Timeline', 'name' => 'action_timeline', 'type' => 'text', 'value' => $repair->action_timeline],
                                ['label' => 'Assigned To', 'name' => 'assigned_to', 'type' => 'text', 'value' => $repair->assigned_to],
                                ['label' => 'Ref', 'name' => 'ref', 'type' => 'text', 'value' => $repair->ref],
                                ['label' => 'Due Date', 'name' => 'due_date', 'type' => 'date', 'value' => $repair->due_date],
                                ['label' => 'Appointment', 'name' => 'appointment', 'type' => 'date', 'value' => $repair->appointment],
                                ['label' => 'Completion Date', 'name' => 'completion_date', 'type' => 'date', 'value' => $repair->completion_date],
                            ];
                        @endphp

                        @foreach ($fields as $field)
                            <div class="col-md-6 col-xl-4">
                                <label>{{ $field['label'] }}</label>
                                <input type="{{ $field['type'] }}" name="{{ $field['name'] }}"
                                    value="{{ old($field['name'], $field['value']) }}" class="form-control" />
                            </div>
                        @endforeach
                                <input type ='hidden' name='block_id' value={{$repair->block->id}}>
                        <div class="col-md-12">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description', $repair->description) }}</textarea>
                        </div>

                        <div class="col-12 text-end mt-3">
                            <button type="submit" class="btn btn-success rounded">Update Repair</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
