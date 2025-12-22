@extends('layouts.dashboard.landpage')

@section('content')
<style>
    th {
        font-size: 12px;
    }
    td {
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
            <h4 class="mb-sm-0 px-1">FOB</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">FOB</a></li>
                    <li class="breadcrumb-item active">View</li>
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

                <h4 class="card-title">Tenant Fob Records</h4>
                <p class="card-title-desc">This table shows all FOBs and their assigned tenants.</p>

                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap"
                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Tenant Name</th>
                            <th>Fob UID</th>
                            <th>Make</th>
                            <th>Model</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Issued Date</th>
                            <th>Fee</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                            @foreach($fobs as $fob)
                                <tr>
                                    <td>{{ $fob->tenant->full_name ?? '-' }}</td>
                                    <td>{{ $fob->fob_uid }}</td>
                                    <td>{{ $fob->make }}</td>
                                    <td>{{ $fob->model }}</td>
                                    <td>{{ ucfirst($fob->type) }}</td>
                                    <td>{{ ucfirst($fob->fob_status) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($fob->issued_date)->format('Y-m-d') }}</td>
                                    <td>£{{ number_format($fob->fee, 2) }}</td>
                                    <td>
                                        <a href="{{ route('fobs.edit', $fob->id) }}" class="btn btn-sm" title="Edit">
                                            <i class="fa fa-pen text-success"></i>
                                        </a>
                                        <form action="{{ route('fobs.destroy', $fob->id) }}" method="POST" class="d-inline delete_btn">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm" title="Delete">
                                                <i class="fas fa-trash-alt delete-btn text-danger"></i>
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