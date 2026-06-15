@extends('layouts.dashboard.landpage')

@section('styles')
<link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/libs/datatables.net-select-bs4/css/select.bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('content')

<style>
    th, td {
        font-size: 12px;
    }
</style>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<!-- Page Header -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 px-1">Branches</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="#">Branches</a></li>
                    <li class="breadcrumb-item active">List</li>
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
                    <h4 class="card-title mb-0">Branch List</h4>
                    <a href="{{ route('branches.create') }}" class="btn btn-success btn-sm">+ Create Branch</a>
                </div>

                <table id="selection-datatable" class="table dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Manager</th>
                            <th>Bank Data</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($branches as $index => $branch)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $branch->name }}</td>
                                <td>{{ $branch->contact_phone ?? 'N/A' }}</td>
                                <td>{{ $branch->contact_email ?? 'N/A' }}</td>
                                <td>{{ $branch->manager_name ?? 'N/A' }}</td>
                                <td> Bank: <b>{{ $branch->bank_name ?? 'N/A' }}</b> <br>
                                    Acct Name:<b>{{$branch->account_name}} </b><br>
                                    Acct Num: <b>{{$branch->account_number}}</b>
                                </td>
                                <td>{{ $branch->created_at->format('d M, Y') }}</td>
                                <td>
                                    <a href="{{ route('branches.edit', $branch->id) }}" class="text-primary me-2">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('branches.destroy', $branch->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm p-0 delete-btn">
                                            <i class="fas fa-trash text-danger"></i>
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
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

<script>
$(function () {
    $('#branch-table').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[6, 'desc']]
    });
});
</script>
@endsection