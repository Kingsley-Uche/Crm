@extends('layouts.dashboard.landpage')

@section('content')
<style>
    th, td {
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
            <h4 class="mb-sm-0 px-1">
    Rent History for {{ $rent_history->first()?->Apartment?->address ?? 'Unknown Address' }}
</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                  <li class="breadcrumb-item"><a href="{{ $referrer }}">{{ $rent_history->title }}</a></li>
                    <li class="breadcrumb-item active">History</li>
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
                <h4 class="card-title">{{$rent_history->title}}</h4>
                <p class="card-title-desc">List of all historical rent accounts with tenant and apartment information.</p>

                <div class="table-responsive">
                    <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Payment Type</th>
                                <th>Property Ref</th>
                                <th>Tenant Name</th>
                                <th>Email</th>
                                <th>Unit Number</th>
                                <th>Payments</th>
                                <th>Status</th>
                                <th>Tenancy Type</th>
                                <th>Escalation Policy</th>
                                <th>Account Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            @foreach ($rent_history as $account)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($account->start_date)->format('Y-m-d') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($account->end_date)->format('Y-m-d') }}</td>
                                    <td>
                                        @if (empty($account->payment_type) || strtolower($account->payment_type) === 'none')
                                            <span class="badge bg-danger">NONE</span>
                                        @else
                                            <span class="badge bg-dark">{{ strtoupper($account->payment_type) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $account->Apartment?->property_ref ?? '-' }}</td>
                                    <td>{{ $account->Tenant?->full_name}}</td>
                                    <td>{{ $account->Tenant?->occupant_email ?? '-' }}</td>
                                    <td>{{ $account->unit_number }}</td>
                                    <td><br>
                                        Rent: <b>£{{ number_format($account->rent_fee, 2) }}</b><br>
                                        Paid: <b>£{{ number_format($account->payment_made, 2) }}</b><br>
                                        Balance: <b>£{{ number_format($account->payment_made - $account->rent_fee, 2) }}</b> 
                                    </td>
                                  <td>
                                @php
                                    $endDate = \Carbon\Carbon::parse($account->end_date);
                                @endphp
                            
                                <span class="badge {{ $endDate->isPast() ? 'bg-danger' : 'bg-success' }}">
                                    {{ $endDate->isPast() ? 'Expired' : 'Active' }} ({{ $endDate->toFormattedDateString() }})
                                </span>
                            </td>

                                    <td>{{ ucfirst($account->Apartment?->tenancy_type ?? '-') }}</td>
                                    <td>{{ $account->escalation_policy ?? '-' }}</td>
                                    <td>{{ $account->account_type }}</td>
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
