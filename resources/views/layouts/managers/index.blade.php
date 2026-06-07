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
@if(session('success'))
<div class="row">
    <div class="col-12">
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    </div>
</div>
@endif

<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Property Managers</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="javascript:void(0);">Managers</a>
                    </li>
                    <li class="breadcrumb-item active">
                        View
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Managers Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <h4 class="card-title">
                            Property Managers
                        </h4>

                        <p class="card-title-desc">
                            This table shows all registered property managers.
                        </p>
                    </div>

                    <div>
                        <a href="{{ route('managers.create') }}"
                           class="btn btn-success">
                            Add Manager
                        </a>
                    </div>
                </div>

                <table id="datatable"
       class="table table-striped table-bordered dt-responsive nowrap"
       style="width:100%">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Date Created</th>
                            <th width="120">Options</th>
                        </tr>
                    </thead>
           <tbody>
@if($managers->count())
    @foreach($managers as $manager)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $manager->name }}</td>
            <td>{{ $manager->email }}</td>
            <td>{{ $manager->phone ?? '-' }}</td>
            <td>{{ $manager->created_at->format('d M Y') }}</td>
            <td>
                 <a href="{{ route('managers.view-assigned-apartments', $manager->id) }}"
                           class="btn btn-sm"
                           title="Assign to Apartments">
                            <i class="fa fa-home text-info"></i>
                        </a>
                <a href="{{ route('managers.edit', $manager->id) }}"
                   class="btn btn-sm"
                   title="Edit">
                    <i class="fa fa-pen text-success"></i>
                        </a>

                <form action="{{ route('managers.destroy', $manager->id) }}"
                      method="POST"
                      style="display:inline-block;"
                      onsubmit="return">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="btn btn-sm delete-btn"
                            title="Delete">
                        <i class="fa fa-trash text-danger"></i>
                    </button>   
            </td>
        </tr>
    @endforeach
@endif


@if($managers->isEmpty())
    <div class="alert alert-info">
        No managers found.
    </div>
@endif
</tbody>
                

                </table>

                

            </div>
        </div>
    </div>
</div>
@endsection
