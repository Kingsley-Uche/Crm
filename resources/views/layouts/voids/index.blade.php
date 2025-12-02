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
            <h4 class="mb-sm-0 px-1">Voids</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Voids</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Voids List</h4>
                <p class="card-title-desc">
                    This displays voids in the past one year. Use the reports module for more data.
                </p>

                <table id="key-datatable" class="table table-bordered table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Days Void</th>
                            <th>Void Classification</th>
                            <th>HFI Code</th>
                            <th>UPRN</th>
                            <th>Property Ref</th>
                            <th>Ten Reason</th>
                            <th>Void Path</th>
                            <th>Void Ref</th>
                            <th>Address</th>
                            <th>Updates</th>
                            <th>Previous Call Over</th>
                            <th>Property Type</th>
                            <th>Property Subtype</th>
                            <th>Bedrooms</th>
                            <th>Void Status</th>
                            <th>VIN SCO Code</th>
                            <th>Termination Date</th>
                            <th>Ready for Let Date</th>
                            <th>Management Unit</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($voids as $index => $void)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $void->days_void ?? 'N/A' }}</td>
                                <td>{{ $void->void_classification ?? 'N/A' }}</td>
                                <td>{{ $void->hfi_code ?? 'N/A' }}</td>
                                <td>{{ $void->uprn ?? 'N/A' }}</td>
                                <td>{{ $void->property_ref ?? 'N/A' }}</td>
                                <td>{{ $void->ten_reason ?? 'N/A' }}</td>
                                <td>{{ $void->void_path ?? 'N/A' }}</td>
                                <td>{{ $void->void_ref ?? 'N/A' }}</td>
                                <td>{{ $void->address ?? 'N/A' }}</td>
                                <td>{{ $void->updates ?? 'N/A' }}</td>
                                <td>{{ $void->previous_call_over ?? 'N/A' }}</td>
                                <td>{{ $void->property_type ?? 'N/A' }}</td>
                                <td>{{ $void->property_subtype ?? 'N/A' }}</td>
                                <td>{{ $void->bedrooms ?? 'N/A' }}</td>
                                <td>{{ $void->void_status ?? 'N/A' }}</td>
                                <td>{{ $void->vin_sco_code ?? 'N/A' }}</td>
                                <td>{{ $void->termination_date ? \Carbon\Carbon::parse($void->termination_date)->format('d M, Y') : 'N/A' }}</td>
                                <td>{{ $void->ready_for_let_date ? \Carbon\Carbon::parse($void->ready_for_let_date)->format('d M, Y') : 'N/A' }}</td>
                                <td>{{ $void->management_unit ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('voids.edit', ['id' => $void->id]) }}" class="btn btn-sm" title="Edit">
                                        <i class='fas fa-pencil-alt text-success'></i>
                                    </a>
                                    <form action="{{ route('voids.destroy', ['id' => $void->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm delete-btn" data-info="{{ $void->void_ref }}" aria-label="Delete Void">
                                            <i class="fas fa-trash-alt text-danger" title="Delete Void"></i>
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
        $(document).ready(function () {
            $('#key-datatable').DataTable({
                pageLength: 25,
                responsive: true
            });
        });
    </script>
@endsection
