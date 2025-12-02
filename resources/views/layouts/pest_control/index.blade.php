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

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Pest Control Reports</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Pest Control</a></li>
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
                <h4 class="card-title">Pest Control Reports</h4>
                <p class="card-title-desc">Review all pest control reports submitted.</p>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped dt-responsive nowrap w-100" id="selection-datatable">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Block</th>
                                <th>Apartment</th>
                                <th>Issue Type</th>
                                <th>Description</th>
                                <th>Reference</th>
                                <th>Status</th>
                                <th>Progress</th>
                                <th>Assigned To</th>
                                <th>Received Date</th>
                                <th>Appointment</th>
                                <th>Due Date</th>
                                <th>Completion Date</th>
                                <th>Pest Control Fee</th>
                                <th>Image</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pests as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->block->name ?? 'N/A' }}</td>
                                    <td>{{ $item->apartment_id ?? 'N/A' }}</td>
                                    <td>{{ $item->issue_type }}</td>
                                    <td>{{ $item->description }}</td>
                                    <td>{{ $item->ref }}</td>
                                    <td>{{ $item->status }}</td>
                                    <td>{{ $item->progress }}</td>
                                    <td>{{ $item->assigned_to }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->received_date)->format('d M, Y h:i A') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->appointment)->format('d M, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->due_date)->format('d M, Y') }}</td>
                                    <td>{{ $item->completion_date ? \Carbon\Carbon::parse($item->completion_date)->format('d M, Y') : 'N/A' }}</td>
                                    <td>{{ number_format($item->pest_control_fee, 2) }}</td>
                                    <td>
                                        @if ($item->image)
                                            <i class="fa-solid fa-image"></i>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('pest_control.show', ['pest_id' => $item->id]) }}" aria-label="Edit Pest Report">
                                            <i class="far fa-edit"></i>
                                        </a>
                                        <form action="{{ route('pest_control.destroy', ['pest_id' => $item->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm delete-btn" data-info="{{ $item->ref }}" aria-label="Delete Pest Report">
                                                <i class="fas fa-trash-alt text-danger" data-toggle="tooltip" title="Delete Pest Report"></i>
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
</div>
@endsection

@section('script')
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-select/js/dataTables.select.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#selection-datatable').DataTable({
                responsive: true,
                pageLength: 10,
                order: [[0, 'asc']],
                columnDefs: [
                    { orderable: false, targets: -1 } // Disable sorting on Options column
                ]
            });

            // Add tooltip for delete button
            $('[data-toggle="tooltip"]').tooltip();

        
        });
    </script>
@endsection