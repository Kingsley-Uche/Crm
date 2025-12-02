@extends('layouts.dashboard.landpage')

@section('content')
<style>
    th, td {
        font-size: 12px;
    }
</style>

<!-- Success Message -->
<div class="row">
    <div class="col-12">
        <div class="card">
            @if (session('success'))
                <div class="alert alert-success m-3">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 px-1">Repairs / Maintenance Report</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('maintenance.report') }}">Reports</a></li>
                    <li class="breadcrumb-item active">Repairs Report</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- DataTable -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Repairs Report</h4>
                <p class="card-title-desc">Kindly use the buttons to export to desired format.</p>

                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap"
                       style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Block</th>
                            <th>Unit</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Repair Type</th>
                            <th>Issue</th>
                            <th>Description</th>
                            <th>Assigned To</th>
                            <th>Ref</th>
                            <th>Due Date</th>
                            <th>Appointment</th>
                            <th>Completion Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($repairs as $repair)
                            <tr>
                                <td>{{ ucwords($repair->block->name ?? '-') }}</td>
                                <td>{{ $repair->unit_number }}</td>
                                <td>{{ $repair->status }}</td>
                                <td>{{ $repair->progress ?: '-' }}</td>
                                <td>{{ $repair->repair_type }}</td>
                                <td>{{ $repair->issue }}</td>
                                <td>{{ Str::limit($repair->description, 100) }}</td>
                                <td>{{ $repair->assigned_to }}</td>
                                <td>{{ $repair->ref ?: '-' }}</td>
                                <td>{{ $repair->due_date ?? '-' }}</td>
                                <td>{{ $repair->appointment ?? '-' }}</td>
                                <td>{{ $repair->completion_date ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
@endsection
