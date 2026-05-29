@extends('layouts.dashboard.landpage')

@section('content')

@if (session('success'))
    <div class="alert alert-success mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="row mb-4">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 px-1">Subscription Plans</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('subscriptions.index') }}">Plans</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm rounded-4 mb-5">
            <div class="card-body p-4">

                <h6 class="text-center text-muted mb-4">Create Subscription Plan</h6>

                <form action="{{ route('subscriptions.store') }}" method="POST">
                    @csrf

                    <div class="row g-4">

                        <!-- Plan Name -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Plan Name <span class="text-danger">*</span></label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Description -->
                        <div class="col-md-6 col-xl-8 mb-3">
                            <label>Description</label>
                            <input type="text" name="description"
                                   class="form-control @error('description') is-invalid @enderror"
                                   value="{{ old('description') }}">
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Limits -->
                        <div class="col-md-6 col-xl-3">
                            <label>Number of Admins <span class="text-danger">*</span></label>
                            <input type="number" name="number_admins"
                                   class="form-control @error('number_admins') is-invalid @enderror"
                                   value="{{ old('number_admins', 0) }}" required>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <label>Number of Branches <span class="text-danger">*</span></label>
                            <input type="number" name="number_branches"
                                   class="form-control @error('number_branches') is-invalid @enderror"
                                   value="{{ old('number_branches', 0) }}" required>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <label>Number of Apartments <span class="text-danger">*</span></label>
                            <input type="number" name="number_apartments"
                                   class="form-control @error('number_apartments') is-invalid @enderror"
                                   value="{{ old('number_apartments', 0) }}" required>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <label>Property Managers <span class="text-danger">*</span></label>
                            <input type="number" name="number_property_managers"
                                   class="form-control @error('number_property_managers') is-invalid @enderror"
                                   value="{{ old('number_property_managers', 0) }}" required>
                        </div>

                        <!-- Pricing -->
                        <div class="col-md-6 col-xl-4">
                            <label>Price Per Month <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="price_per_month"
                                   class="form-control @error('price_per_month') is-invalid @enderror"
                                   value="{{ old('price_per_month') }}" required>
                        </div>

                        <!-- Discount -->
                        <div class="col-md-6 col-xl-4">
                            <label>Discount Min Months</label>
                            <input type="number" name="discount_min_months"
                                   class="form-control @error('discount_min_months') is-invalid @enderror"
                                   value="{{ old('discount_min_months') }}">
                        </div>

                        <div class="col-md-6 col-xl-4">
                            <label>Discount Percentage (%)</label>
                            <input type="number" step="0.01" name="discount_percentage"
                                   class="form-control @error('discount_percentage') is-invalid @enderror"
                                   value="{{ old('discount_percentage') }}">
                        </div>

                        <!-- Status -->
                        <div class="col-md-6 col-xl-4">
                            <label>Status <span class="text-danger">*</span></label>
                            <select name="is_active" class="form-control @error('is_active') is-invalid @enderror" required>
                                <option value="1" {{ old('is_active') == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <!-- Submit -->
                        <div class="col-12 text-end mt-4">
                            <button type="submit" class="btn btn-success rounded">
                                Create Plan
                            </button>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection