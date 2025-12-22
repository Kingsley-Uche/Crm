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
            <h4 class="mb-sm-0 px-1">Rent Cycle Records</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                     <li class="breadcrumb-item"><a href="{{ route('rent.report') }}">Reports</a></li>
                    <li class="breadcrumb-item active">Rent Report</li>
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

                <h4 class="card-title">Rent Report</h4>
                <p class="card-title-desc">Click any the buttons to export to a desired format.</p>

                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap"
                       style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Rent Fee (£)</th>
                            <th>Paid (£)</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Payment Method</th>
                            <th>Address</th>
                            <th>Post Code</th>
                            <th>Property Ref</th>
                            <th>Tenancy Type</th>
                            <th>Ownership</th>
                            <th>Admin Unit</th>
                            <th>Unique Code</th>
                            <th>Mobile</th>
                            <th>Home</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($flatReport as $row)
                            <tr>
                                <td>{{ $row['cycle_start_date'] }}</td>
                                <td>{{ $row['cycle_end_date'] }}</td>
                                <td>£{{ number_format($row['rent_fee'], 2) }}</td>
                                <td>£{{ number_format($row['payment_made'], 2) }}</td>
                                <td>{{ $row['full_name'] }}</td>
                                <td>{{ $row['occupant_email'] }}</td>
                                <td>{{ strtoupper($row['payment_method']) }}</td>
                                <td>{{ ucwords($row['address']) }}</td>
                                <td>{{ $row['post_code'] }}</td>
                                <td>{{ $row['property_ref'] }}</td>
                                <td>{{ $row['tenancy_type'] }}</td>
                                <td>{{ $row['ownership'] }}</td>
                                <td>{{ $row['admin_unit'] }}</td>
                                <td>{{ $row['unique_code'] }}</td>
                                <td>{{ $row['mobile_number'] }}</td>
                                <td>{{ $row['home_number'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
@endsection
