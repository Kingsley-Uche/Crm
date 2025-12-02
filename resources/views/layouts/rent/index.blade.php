@extends('layouts.dashboard.landpage')

@section('content')
    <style>
        th, td {
            font-size: 12px;
        }
    </style>

    <!-- Success Message -->
    @if (session('success'))
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="alert alert-success m-3" role="alert">
                        {{ session('success') }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 px-1">Apartment Records</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Apartments</a></li>
                        <li class="breadcrumb-item active">View</li>
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
                    <h4 class="card-title">Apartment Records</h4>
                    <p class="card-title-desc">This table shows apartment details.</p>

                    <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap"
                           style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>Property Ref</th>
                                <th>Shelter</th>
                                <th>Unit Number</th>
                                <th>Address</th>
                                <th>Post Code</th>
                                <th>Fee</th>
                                <th>Amenities</th>
                                <th>Unique Code</th>
                                <th>Payment Date</th>
                                <th>Expiry</th>
                               
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($apartments as $apartment)
                                <tr>
                                    <td>{{ $apartment->property_ref ?? '-' }}</td>
                                    <td>{{ ucfirst($apartment->shelter->name ?? '-') }}</td>
                                    <td>{{ $apartment->unit_number ?? '-' }}</td>
                                    <td>{{ $apartment->address ? ucwords($apartment->address) : '-' }}</td>
                                    <td>{{ $apartment->post_code ?? '-' }}</td>
                                    <td>
                                        @if(!is_null($apartment->fee))
                                            £{{ number_format($apartment->fee, 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($apartment->AmenitySize))
                                            @foreach($apartment->AmenitySize as $amenity_size)
                                                {{ ucfirst($amenity_size->amenity_name ?? 'N/A') }}: {{ $amenity_size->amenity_size ?? '-' }}<br>
                                            @endforeach
                                        @else
                                            No Amenities
                                        @endif
                                    </td>
                                    <td>{{ $apartment->unique_code ?? '-' }}</td>
                                    <td>{{ $apartment->bookStatus->start_date ?? '-' }}</td>
                                    <td>{{ $apartment->bookStatus->end_date ?? '-' }}</td>
                                   
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection