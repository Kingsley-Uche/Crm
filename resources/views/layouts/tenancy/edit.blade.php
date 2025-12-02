@extends('layouts.dashboard.landpage')

@section('content')
<style>
    th, td {
        font-size: 12px;
    }
    .error {
        color: red;
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
            <h4 class="mb-sm-0">Edit Tenancy Type</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Utility</a></li>
                    <li class="breadcrumb-item active">Tenancy Type</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Edit Form -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Tenancy Type</h4> 
                <form action="{{ route('tenancy.update', $tenancyType->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <label for="tenancy_type" class="col-sm-2 col-form-label">Tenancy Type</label>
                        <div class="col-sm-8">
                            <input class="form-control" type="text" name="name" value="{{ old('name', $tenancyType->name) }}" placeholder="Enter tenancy type">
                            @error('name')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
