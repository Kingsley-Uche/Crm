@extends('layouts.dashboard.landpage')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4>Edit Subscription Account</h4>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">

        <form action="{{ route('subscription.account.update', $account->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">

                <!-- Plan -->
                <div class="col-md-6">
                    <label>Plan</label>
                    <select name="plan_id" class="form-control" required>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}"
                                {{ $account->plan_id == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Fee -->
                <div class="col-md-6">
                    <label>Fee</label>
                    <input type="number" step="0.01" name="fee"
                           value="{{ $account->fee }}"
                           class="form-control" required>
                </div>

                <!-- Status -->
                <div class="col-md-6">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="active" {{ $account->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $account->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="expired" {{ $account->status == 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="pending" {{ $account->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>

                <!-- Start -->
                <div class="col-md-6">
                    <label>Start Time</label>
                    <input type="datetime-local" name="start_time"
                           value="{{ $account->start_time ? date('Y-m-d\TH:i', strtotime($account->start_time)) : '' }}"
                           class="form-control">
                </div>

                <!-- End -->
                <div class="col-md-6">
                    <label>End Time</label>
                    <input type="datetime-local" name="end_time"
                           value="{{ $account->end_time ? date('Y-m-d\TH:i', strtotime($account->end_time)) : '' }}"
                           class="form-control">
                </div>

                <div class="col-12 text-end">
                    <button class="btn btn-primary">Update Account</button>
                </div>

            </div>

        </form>

    </div>
</div>

@endsection