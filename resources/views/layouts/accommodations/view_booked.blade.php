@extends('layouts.dashboard.landpage')

@section('content')

<style>
    .small-table th, .small-table td {
        font-size: 12px;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="card px-1 py-1">
            <div class="d-flex justify-content-between align-items-center mb-3 mx-2">
                <h5 class="card-title mb-0 ">Booked Records</h5>
                <p class="card-title-desc m-2">View and manage booking information for tenants and landlords.</p>
            </div>
            

            <div class="table-responsive">
                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap small-table">
                    <thead class="table-light">
                        <tr>
                            <th>S/N</th>
                            <th>Shelter Name</th>
                            <th>Shelter Address</th>
                            <th>Tenant Name</th>
                            <th>Tenant Gender</th>
                            <th>Tenant Nationality</th>
                            <th>State</th>
                            <th>Phone Numbers</th>
                            <th>Tenant D.O.B</th>
                            <th>Email</th>
                            <th>Booked From</th>
                            <th>Booked Till</th>
                            <th>Block State</th>
                            <th>Block LGA</th>
                            <th>Block Country</th>
                            <th>Landlord Full Name</th>
                            <th>Landlord Phones</th>
                            <th>Cancel:</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($booked as $booking)
                            <tr>
                                <td>{{ $loop->iteration + ($booked->currentPage() - 1) * $booked->perPage() }}</td>
                                <td>{{ucfirst( $booking->shelter_name) }}</td>
                                <td>{{ ucfirst($booking->block_address) }}</td>
                              <td>{{ ucfirst($booking->first_name) }} {{ ucfirst(substr($booking->middle_name, 0, 1)) }}. {{ ucfirst($booking->last_name) }}</td>
                                <td>{{ ucfirst($booking->gender) }}</td>
                                <td>{{ $booking->nationality }}</td>
                                <td>{{ $booking->state }}</td>
                                <td>{{ $booking->mobile_number }}</td>
                                <td>{{\Carbon\Carbon::parse($booking->date_of_birth)->format('M d, Y') }}</td>
                                <td>{{ $booking->tenant_email }}</td>
                                <td><span class="badge bg-info">{{ \Carbon\Carbon::parse($booking->booked_from)->format('M d, Y') }}</span></td>
                               <td><span class="badge bg-warning">{{ \Carbon\Carbon::parse($booking->booked_to)->format('M d, Y') }}</span></td>
                                 <td>{{ $booking->block_state }}</td>
                                <td>{{ $booking->block_lgvt }}</td>
                                <td>{{ $booking->block_country }}</td>
                                <td>{{ $booking->landlord_fname }} {{ $booking->landlord_lname }}</td>
                                <td>{{ $booking->landlord_phones }}</td>
                                <td>
                                    <form action="{{ route('booked.cancel', $booking->booking_id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <a href="{{ route('booked.cancel', $booking->booking_id) }}" class="btn btn-sm delete-btn" data-fname="{{$booking->first_name }}" data-lname="{{$booking->last_name}}" aria-label="Cancel booking">
                                     <i class="fas fa-times-circle text-danger" data-toggle="tooltip" title="Cancel booking"></i> 
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="d-flex justify-content-end mt-3">
                {{ $booked->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>




    <!-- Pagination -->
   
@endsection

@section('scripts')

@endsection
