@extends('layouts.dashboard.landpage')

@section('content')
<style>
    th, td {
        font-size: 12px;
    }
</style>

@if (session('success'))
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="alert alert-success m-3" role="alert">
                    {{ session('success') }}
                </div>
            </div>
        </div>
    </div>
@endif

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 px-1">Role Management</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Access Control</a></li>
                    <li class="breadcrumb-item active">Roles</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Roles List</h4>
                <p class="card-title-desc">This table shows all defined roles.</p>

                <div class="d-flex mb-3">
                    <a href="{{ route('access.roles.create') }}" class="btn btn-success ms-auto">
                        <i class="fas fa-plus text-white"></i> Add New Role
                    </a>
                </div>

                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap"
                       style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Created At</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td>{{ $role->name ?? 'N/A' }}</td>
                                <td>{{ $role->description ?? '-' }}</td>
                                <td>{{ $role->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    <a href="{{ route('access.roles.edit', $role->id) }}" class="btn btn-sm" title="Edit">
                                        <i class="fas fa-pen text-success"></i>
                                    </a>
                                    <form action="{{ route('access.roles.destroy', $role->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm delete-btn" title="Delete">
                                            <i class="fas fa-trash-alt text-danger"></i>
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
