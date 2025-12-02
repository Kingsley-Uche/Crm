@extends('layouts.dashboard.landpage')

@section('content')
<style>
    th, td {
        font-size: 12px;
    }
    .table-responsive-flex {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
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
            <h4 class="mb-sm-0 px-1">Rent Accounts</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="#">Rent</a></li>
                    <li class="breadcrumb-item active">Accounts</li>
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
                <h4 class="card-title">{{$rent_accounts->title}}</h4>
                <p class="card-title-desc">List of rent accounts with tenants and apartment info.</p>

                <div class="table-responsive-flex">
                    <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Address</th>
                                <th>Start Date</th>
                                <th>Status</th>
                                <th>Property Ref</th>
                                <th>Tenant Name</th>
                                <th>Email</th>
                                <th>Unit Number</th>
                                <th>Payment Details</th>
                                <th>Tenancy Type</th>
                                <th>Ownership</th>
                                <th>Account Type</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rent_accounts as $account)
                                <tr>
                                    <td>{{ ucwords($account->Apartment->address) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($account->start_date)->format('Y-m-d') }}</td>
                                    <td><span class="badge bg-{{$account->color}}">{{ ucfirst($account->status) }}</span></td>
                                    <td>{{ $account->Apartment->property_ref ?? '-' }}</td>
                                    <td>{{ $account->Tenant->first_name ?? '-' }} {{ $account->Tenant->last_name ?? '' }}</td>
                                    <td>{{ $account->Tenant->occupant_email ?? '-' }}</td>
                                    <td>{{ $account->unit_number }}</td>
                                    <td><br>
                                        Total Rent: <b>£ {{ $account->total_fee }} </b><br>
                                        Paid: <b>£ {{ $account->total_paid }}</b> <br>
                                        Balance: <b>£ {{ $account->balance }}</b>
                                    </td>
                                    <td>{{ ucfirst($account->Apartment->tenancy_type ?? '-') }}</td>
                                    <td>{{ $account->Apartment->ownership ?? '-' }}</td>
                                    <td>{{ $account->account_type }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route($rent_accounts->url, $account->id) }}" title="View rent history" class="btn btn-rounded">
                                                <i class="fas fa-receipt text-success"></i>
                                            </a>
                                           @if(isset($account->color) && $account->color === 'success')
                                    <form action="{{ route('rent_accounts.deactivate', $account->id) }}" method="POST" class="deactivate-form">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-rounded confirm-deactivate" title="Deactivate Account"
                                            onclick="return confirm('Are you sure you want to deactivate?')">
                                            <i class="fas fa-user-slash text-danger"></i>
                                        </button>
                                    </form>
                                @endif

                                        </div>
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

