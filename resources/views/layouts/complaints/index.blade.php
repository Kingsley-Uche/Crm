@extends('layouts.dashboard.landpage')

@section('styles')
    <link href="{{ asset('assets/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/datatables.net-select-bs4/css/select.bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<style>
    th, td {
        font-size: 12px;
    }
</style>

<!-- Success Message -->
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 px-1">Complaints</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Complaints</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Complaints List</h4>
                <p class="card-title-desc">
                    View all submitted complaints and manage them accordingly.
                </p>

                <table id="selection-datatable" class="table dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Block</th>
                            <th>Apartment</th>
                            <th>Unit Number</th>
                            <th>Tenant</th>
                            <th>Complainant's Phone</th>
                            <th>Complainant's Email</th>
                            <th>Subject</th>
                            <th>Action Taken</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th>Received Date</th>
                            <th>Resolved Date</th>
                            <th>Description</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($complaints as $index => $complaint)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ ucwords($complaint->block->name) ?? 'N/A' }}</td>
                                <td>{{ $complaint->apartment_id ?? 'N/A' }}</td>
                                <td>{{ $complaint->unit_number ?? 'N/A' }}</td>
                                <td>{{ ucwords($complaint->tenant->first_name) ?? '' }} {{ $complaint->tenant->last_name ?? '' }}</td>
                                <td>{{ $complaint->phone ?? 'N/A' }}</td>
                                <td>{{ $complaint->email ?? 'N/A' }}</td>
                                <td>{{ $complaint->subject ?? 'N/A' }}</td>
                                 <td>{{ $complaint->action_taken ?? 'N/A' }}</td>
                                <td>{{ ucfirst($complaint->status) }}</td>
                                <td>{{ $complaint->assigned_to ?? 'N/A' }}</td>
                                <td>{{ $complaint->received_date ? \Carbon\Carbon::parse($complaint->received_date)->format('d M, Y') : 'N/A' }}</td>
                                <td>{{ $complaint->resolved_date ? \Carbon\Carbon::parse($complaint->resolved_date)->format('d M, Y') : 'N/A' }}</td>
                                <td>{{ Str::limit($complaint->description, 30) ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('complaints.edit', $complaint->id) }}"><i class='fas fa-pencil-alt'></i></a>
                                    <form action="{{ route('complaints.destroy', $complaint->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm delete-btn" data-info="{{ $complaint->subject }}" aria-label="Delete Complaint">
                                            <i class="fas fa-trash-alt text-danger" data-toggle="tooltip" title="Delete Complaint"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-select/js/dataTables.select.min.js') }}"></script>
    <script>
        $(function() {
            "use strict";
            $('#selection-datatable').DataTable({
                select: {
                    style: 'multi',
                    selector: 'tr'
                },
                responsive: true,
                pageLength: 10,
                order: [[10, 'desc']], // Order by Received Date
                scrollX: true,
                columnDefs: [
                    { width: '150px', targets: [12] }, // Description
                    { width: '80px', targets: [0, 3, 4, 5, 6, 7, 8, 10, 11] }
                ]
            });
        });
    </script>
@endsection
