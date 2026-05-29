@extends('layouts.dashboard.landpage')

@section('styles')
    <link href="{{ asset('assets/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/datatables.net-select-bs4/css/select.bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('content')

<style>
    th, td {
        font-size: 12px;
    }
</style>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Subscription Accounts</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">Subscriptions</li>
                    <li class="breadcrumb-item active">Accounts</li>
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
                    <div>
                        <h4 class="card-title">Accounts</h4>
                        <p class="card-title-desc">Manage subscription accounts assigned to trackers.</p>
                    </div>

                    <a href="{{ route('subscription.account.create') }}" class="btn btn-success btn-sm">
                        + Create Account
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped dt-responsive nowrap w-100" id="selection-datatable">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Plan</th>
                                <th>Tracker ID</th>
                                <th>Fee</th>
                                <th>Status</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Plan Info</th>
                                <th>Created</th>
                                <th>Options</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($accounts as $index => $account)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $account->plan->name ?? 'N/A' }}</td>
                                    <td>{{ $account->tracker_id }}</td>
                                    <td>₦{{ number_format($account->fee, 2) }}</td>

                                    <td>
                                        @if($account->status == 'active')
                                            <span class="badge bg-success">Active</span>
                                        @elseif($account->status == 'inactive')
                                            <span class="badge bg-secondary">Inactive</span>
                                        @elseif($account->status == 'expired')
                                            <span class="badge bg-danger">Expired</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>

                                    <td>{{ $account->start_time ? \Carbon\Carbon::parse($account->start_time)->format('d M, Y') : 'N/A' }}</td>
                                    <td>{{ $account->end_time ? \Carbon\Carbon::parse($account->end_time)->format('d M, Y') : 'N/A' }}</td>
                                    <td>
                                        <strong></strong> {{ $account->plan->name ?? 'N/A' }}<br>
                                        Price: ₦{{ number_format($account->plan->price_per_month ?? 0, 2) }}<br>
                                        Admins: {{ $account->plan->number_admins ?? 0 }}<br>
                                        Branches: {{ $account->plan->number_branches ?? 0 }}<br>
                                        Apartments: {{ $account->plan->number_apartments ?? 0 }}<br>
                                        Managers: {{ $account->plan->number_property_managers ?? 0 }}<br>
                                        Discount: {{ $account->plan->discount_percentage ?? 0 }}%   
                                    </td>
                                    <td>{{ $account->created_at->format('d M, Y') }}</td>

                                    <td>
                                        <a href="{{ route('subscription.account.edit', $account->id) }}">
                                            <i class="fas fa-pencil-alt text-success"></i>
                                        </a>

                                        <form action="{{ route('subscription.account.destroy', $account->id) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm delete-btn"
                                                    onclick="return confirm('Delete this account?')">
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
</div>

@endsection

@section('script')
<script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

<script>
    $(document).ready(function () {
        $('#selection-datatable').DataTable();
    });
</script>
@endsection