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
            <h4 class="mb-sm-0 px-1">Pest Control Report</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ request()->routeIs('pest_control.report') }}">Reports</a></li>
                    <li class="breadcrumb-item active">Pest Control</li>
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

                <h4 class="card-title">Pest Control Report</h4>
                <p class="card-title-desc">This table shows pest control requests filtered by date.</p>

                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap"
                       style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Block</th>
                            <th>Unit Number</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Issue Type</th>
                            <th>Description</th>
                            <th>Assigned To</th>
                            <th>Ref</th>
                            <th>Due Date</th>
                            <th>Appointment</th>
                            <th>Completion Date</th>
                            <th>Control Fee</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pests as $pest)
                            <tr>
                                <td>{{ ucfirst($pest->block->name ?? 'N/A') }}</td>
                                <td>{{ $pest->apartment->unit_number?? 'N/A' }}</td>
                                <td>{{ $pest->status ?? '-' }}</td>
                                <td>{{ $pest->progress ?? '-' }}</td>
                                <td>{{ $pest->issue_type ?? '-' }}</td>
                                <td>{{ Str::limit($pest->description, 100) }}</td>
                                <td>{{ $pest->assigned_to ?? '-' }}</td>
                                <td>{{ $pest->ref ?? '-' }}</td>
                                <td>{{ $pest->due_date ?? '-' }}</td>
                                <td>{{ $pest->appointment ?? '-' }}</td>
                                <td>{{ $pest->completion_date ?? '-' }}</td>
                                <td>£{{ number_format($pest->pest_control_fee ?? 0, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
@endsection
