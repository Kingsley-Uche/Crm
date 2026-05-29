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
            <h4 class="mb-sm-0 px-1">Edit Subscription Plan</h4>

            <div class="page-title-right">
                <a href="{{ route('subscriptions.index') }}" class="btn btn-secondary btn-sm">
                    Back
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm rounded-4">
    <div class="card-body p-4">

        <form action="{{ route('subscriptions.update', $plan->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-4">

                <!-- Name -->
                <div class="col-md-6 col-xl-4">
                    <label>Plan Name</label>
                    <input type="text" name="name"
                           value="{{ old('name', $plan->name) }}"
                           class="form-control @error('name') is-invalid @enderror"
                           required>
                </div>

                <!-- Description -->
                <div class="col-md-6 col-xl-8">
                    <label>Description</label>
                    <input type="text" name="description"
                           value="{{ old('description', $plan->description) }}"
                           class="form-control">
                </div>

                <!-- Limits -->
                <div class="col-md-6 col-xl-3">
                    <label>Admins</label>
                    <input type="number" name="number_admins"
                           value="{{ old('number_admins', $plan->number_admins) }}"
                           class="form-control" required>
                </div>

                <div class="col-md-6 col-xl-3">
                    <label>Branches</label>
                    <input type="number" name="number_branches"
                           value="{{ old('number_branches', $plan->number_branches) }}"
                           class="form-control" required>
                </div>

                <div class="col-md-6 col-xl-3">
                    <label>Apartments</label>
                    <input type="number" name="number_apartments"
                           value="{{ old('number_apartments', $plan->number_apartments) }}"
                           class="form-control" required>
                </div>

                <div class="col-md-6 col-xl-3">
                    <label>Property Managers</label>
                    <input type="number" name="number_property_managers"
                           value="{{ old('number_property_managers', $plan->number_property_managers) }}"
                           class="form-control" required>
                </div>

                <!-- Pricing -->
                <div class="col-md-6 col-xl-4">
                    <label>Price Per Month</label>
                    <input type="number" step="0.01"
                           name="price_per_month"
                           value="{{ old('price_per_month', $plan->price_per_month) }}"
                           class="form-control" required>
                </div>

                <!-- Discount -->
                <div class="col-md-6 col-xl-4">
                    <label>Discount Min Months</label>
                    <input type="number"
                           name="discount_min_months"
                           value="{{ old('discount_min_months', $plan->discount_min_months) }}"
                           class="form-control">
                </div>

                <div class="col-md-6 col-xl-4">
                    <label>Discount Percentage</label>
                    <input type="number" step="0.01"
                           name="discount_percentage"
                           value="{{ old('discount_percentage', $plan->discount_percentage) }}"
                           class="form-control">
                </div>

                <!-- Status -->
                <div class="col-md-6 col-xl-4">
                    <label>Status</label>
                    <select name="is_active" class="form-control" required>
                        <option value="1" {{ $plan->is_active ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$plan->is_active ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Submit -->
                <div class="col-12 text-end mt-4">
                    <button class="btn btn-success" type="submit">
                        Update Plan
                    </button>
                </div>

            </div>
        </form>

    </div>
</div>

@endsection