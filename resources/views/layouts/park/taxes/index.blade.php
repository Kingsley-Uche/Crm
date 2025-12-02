```blade
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
            <h4 class="mb-sm-0">Tax Records</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Taxes</a></li>
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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="card-title">All Taxes</h4>
                        <p class="card-title-desc">Available tax records</p>
                    </div>
                    <a href="{{ route('park.taxes.create') }}" class="btn btn-success">
                        <i class="fa fa-plus text-white"></i> Add New Tax
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped dt-responsive nowrap w-100" id="selection-datatable">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Tax Name</th>
                                <th>Percentage (%)</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($taxes as $index => $tax)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $tax->name }}</td>
                                    <td>{{ number_format($tax->percentage, 2) }}%</td>
                                    <td>
                                        <a href="{{ route('park.taxes.edit', ['tax_id' => $tax->id]) }}"><i class='fas fa-pencil-alt text-success'></i></a>
                                        <form action="{{ route('park.taxes.destroy', ['tax_id' => $tax->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm delete-btn" data-info="{{ $tax->name }}" aria-label="Delete Tax">
                                                <i class="fas fa-trash-alt text-danger" data-toggle="tooltip" title="Delete Tax"></i>
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
            $('.table').DataTable();
        });
    </script>
@endsection
```