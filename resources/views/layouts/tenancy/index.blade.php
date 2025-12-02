@extends('layouts.dashboard.landpage')

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
            <h4 class="mb-sm-0 px-1">Tenancy Type</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Tenancy Type</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Tenancy Types Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
            
                
                  <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="card-title">Tenancy Type</span>
            <a href="{{ route('tenancy.show') }}" class="btn btn-success">Create Tenancy Type</a>
        </div>

                <div class="table-responsive">
                    <table class="table table-editable table-nowrap align-middle table-edits">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tenancyTypes as $index=> $tenancyType) 
                                <tr data-id="{{ $tenancyType->id }}">
                                    <td data-field="id">{{ $index+1 }}</td>
                                    <td data-field="name">{{ ucfirst($tenancyType->name) }}</td>
                                    <td style="width: 100px">
                                        <a href="{{ route('tenancy.edit', $tenancyType->id) }}" 
                                           class="btn btn-sm" title="Edit">
                                            <i class="fas fa-pencil-alt text-success"></i>
                                        </a>
                                        <form action="{{ route('tenancy.destroy', $tenancyType->id) }}" 
                                              method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn delete-btn btn-sm" 
                                                    title="Delete">
                                                <i class="fas fa-trash-alt text-danger"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $tenancyTypes->links() }}
                </div>
            </div>
        </div>
    </div> 
</div>

@endsection

@section('script')
<script src="{{ asset('assets/libs/table-edits/build/table-edits.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/table-editable.init.js') }}"></script> 
@endsection
