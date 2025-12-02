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
            <h4 class="mb-sm-0">Parking Records</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Parking Records</a></li>
                    <li class="breadcrumb-item active">Create</li>
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
                        <h4 class="card-title">All Parks</h4>
                        <p class="card-title-desc">Available parks</p>
                    </div>
                    <a href="{{ route('park.models.create') }}" class="btn btn-success">
                        <i class="fa fa-plus text-white"></i> Add New Park
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped dt-responsive nowrap w-100" id='selection-datatable'>
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Park Name</th>
                                <th>Location</th>
                                <th>Address</th>
                                <th>Category</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($parks as $index => $park)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $park->name }}</td>
                                    <td>{{ $park->location }}</td>
                                    <td>{{ $park->address}}</td>
                                    <td>{{ $park->category->name}}</td>
                                    <td>
                                        <a href="{{ route('park.models.edit', ['park_id' => $park->id]) }}"><i class='fas fa-pencil-alt text-success'></i></a>
                                        <form action="{{ route('park.models.destroy', ['park_id' => $park->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm delete-btn" data-info="{{ $park->name }}" aria-label="Delete Park">
                                                <i class="fas fa-trash-alt text-danger" data-toggle="tooltip" title="Delete Park"></i>
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