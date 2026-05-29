@extends('layouts.dashboard.landpage')

@section('content')

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Create Subscription Account</h4>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">

        <form action="{{ route('subscription.account.store') }}" method="POST">
            @csrf

            <div class="row g-3">

                <!-- Plan -->
                <div class="col-md-6">
                    <label>Plan</label>
                    <select name="plan_id" class="form-control" required>
                        <option value="">Select Plan</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Fee -->
                <div class="col-md-6">
                    <label>Fee</label>
                    <input type="number" step="0.01" name="fee" class="form-control" required>
                </div>

                <!-- Status -->
                <div class="col-md-6">
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="expired">Expired</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>

                <!-- Start -->
                <div class="col-md-6">
                    <label>Start Time</label>
                    <input type="datetime-local" name="start_time" class="form-control">
                </div>

                <!-- End -->
                <div class="col-md-6">
                    <label>End Time</label>
                    <input type="datetime-local" name="end_time" class="form-control">
                </div>

                <div class="col-12 text-end">
                    <button class="btn btn-success">Create Account</button>
                </div>

            </div>

        </form>

    </div>
</div>

@endsection