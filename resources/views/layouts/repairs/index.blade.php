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
            <h4 class="mb-sm-0 px-1">Repairs</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Repairs</a></li>
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
                <h4 class="card-title">Repairs List</h4>
                <p class="card-title-desc">
                    View and select multiple repair records. Click on a row to toggle its selected state.
                </p>

                <table id="selection-datatable" class="table dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Block</th>
                            <th>Address</th>
                            <th>Received Date</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th>Repair Type</th>
                            <th>Deadline Timeframe</th>
                            <th>Issue</th>
                            <th>Appointment Timeframe</th>
                            <th>Description</th>
                            <th>Action Timeline</th>
                            <th>Assigned To</th>
                            <th>Reference</th>
                            <th>Due Date</th>
                            <th>Appointment</th>
                            <th>Completion Date</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($repairs as $index=>$repair)
                            <tr>
                                <td>{{ $index+1 }}</td>
                                <td>{{ ucwords($repair->block->name) ?? 'N/A' }}</td>
                                <td>{{ $repair->unit_number }}</td>
                                <td>{{ $repair->received_date ? \Carbon\Carbon::parse($repair->received_date)->format('d M, Y') : 'N/A' }}</td>
                                <td>{{ $repair->progress ?: 'N/A' }}</td>
                                <td>{{ $repair->status ?: 'N/A' }}</td>
                                <td>{{ $repair->repair_type ?: 'N/A' }}</td>
                                <td>{{ $repair->deadline_timeframe ?: 'N/A' }}</td>
                                <td>{{ $repair->issue ?: 'N/A' }}</td>
                                <td>{{ $repair->appointment_timeframe ?: 'N/A' }}</td>
                                <td>{{ $repair->description ?: 'N/A' }}</td>
                                <td>{{ $repair->action_timeline ?: 'N/A' }}</td>
                                <td>{{ $repair->assigned_to ?: 'N/A' }}</td>
                                <td>{{ $repair->ref ?: 'N/A' }}</td>
                                <td>{{ $repair->due_date ? \Carbon\Carbon::parse($repair->due_date)->format('d M, Y') : 'N/A' }}</td>
                                <td>{{ $repair->appointment ? \Carbon\Carbon::parse($repair->appointment)->format('d M, Y') : 'N/A' }}</td>
                                <td>{{ $repair->completion_date ? \Carbon\Carbon::parse($repair->completion_date)->format('d M, Y') : 'N/A' }}</td>
                                 <td>
    <a href="{{ route('maintenance.edit', ['repair_id' => $repair->id]) }}"><i class='fas fa-pencil-alt'></i></a>
   <form action="{{ route('maintenance.destroy', ['repair_id' => $repair->id]) }}" method="POST" class="d-inline">
    @csrf
    @method('DELETE')
 
 <button type="submit" class="btn btn-sm delete-btn" data-info="{{ $repair->repair_type }}" aria-label="Delete Repairs">
        <i class="fas fa-trash-alt text-danger" data-toggle="tooltip" title="Delete Repair"></i>
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

            // Initialize datepicker on click
            let datepickerInitialized = false;
            $('#datepicker6 input[name="start"], #datepicker6 input[name="end"]').on('click', function() {
                if (!datepickerInitialized) {
                    console.log('Initializing datepicker');
                    $('.input-daterange').datepicker({
                        format: 'dd M, yyyy',
                        autoclose: true,
                        container: '#datepicker6'
                    });
                    datepickerInitialized = true;
                    $(this).datepicker('show');
                }
            });

            // Initialize DataTable with multi-item selection
            $('#selection-datatable').DataTable({
                select: {
                    style: 'multi',
                    selector: 'tr'
                },
                responsive: true,
                pageLength: 10,
                order: [[4, 'desc']], // Sort by Received Date (column 4) descending
                scrollX: true, // Enable horizontal scrolling for many columns
                columnDefs: [
                    { width: '100px', targets: [11] }, // Adjust width for Description column
                    { width: '80px', targets: [1, 3, 5, 6, 7, 9, 13, 14] } // Adjust width for shorter columns
                ]
            });
        });
    </script>
@endsection