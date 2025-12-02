@extends('layouts.dashboard.landpage')
<style>
.badge {
    display: inline-block;
    max-width: 100%; /* Make sure the badge doesn't overflow */
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-transform: capitalize;
    font-weight:300px;
}
#apartment_name {
  text-transform: capitalize;
}

</style>
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between mb-1 w-100">
                <div class="page-title-right w-100">
                    <div class="card border-0 shadow-sm p-3">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title mb-2">
                                    Estate Owner: 
                                    <span class="text-success">{{ ucwords($blockShelter->estateOwner->fName) }} {{ ucwords($blockShelter->estateOwner->lName) }}</span>
                                </h4>
                                <p><strong>Contact:</strong> {{ $blockShelter->estateOwner->phones }} | {{ $blockShelter->estateOwner->email }}</p>
                                <p><strong>Block Address:</strong> {{ ucwords($blockShelter->block->address) }}</p>
                            </div>

                            <!-- Breadcrumb for Building and Apartment Info -->
                            <div class="col-md-6 text-md-end mt-4 mt-md-0">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('shelters.index') }}" class="text-decoration-none">
                                            <i class="fas fa-house-user text-muted"></i> Building Title: {{ ucwords($blockShelter->block->name) }}
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item active">Apartment: {{ ucwords($blockShelter->shelter->name) }} h/li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 

    <!-- Booking Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white p-2 rounded text-center">
                        Book Apartment: <span id='apartment_name'></span>
                    </h5>
                    <span class="badge rounded bg-warning float-end">$ <span id='fee'>{{ number_format(0, 2) }}</span></span>
                    <button type="button" class="btn-close  btn btn-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Form starts here -->
                <form id="bookingForm" method="POST" action="{{ route('accommodation.book') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="start-date-input" class="form-label">Start Date</label>
                            <input class="form-control" type="date" id="start-date-input" name="start_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="end-date-input" class="form-label">End Date</label>
                            <input class="form-control" type="date" id="end-date-input" name="end_date" required>
                        </div>
                        <input type="hidden" id="apartment_id" name="apartment_id">
                        <input type="hidden" id="pay_time_id" name="payment_time_id">
                        <input type="hidden" id="shelter_id" name="shelter_id">
                        <input type="hidden" id="block_id" name="block_id">
                        <input type="hidden" id="block_shelter" name="block_shelter_id">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="tenants">Select Tenant</label>
                        <select name="tenant_id" class="form-control" id="tenants" required>
                            <option value="">Select a Tenant</option>
                            @foreach($tenants as $tenant)
                                <option value="{{ $tenant->id }}">{{ $tenant->first_name . ' ' . $tenant->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success" id="manage">Confirm Booking</button>
                    </div>
                </form>
                <!-- Form ends here -->
            </div>
        </div>
    </div>

    <!-- Apartments List -->
    <div class="row">
        @foreach($apartments as $index => $apartment)
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title text-center">
                            <i class="fas fa-house-user text-warning"></i> 
                            {{ucwords($apartment->name ?? $blockShelter->shelter->name).' '.$apartment->unit_number }}
                            <br><span class='badge bg-warning'>{{$apartment->address}}</span><br>
                        </h6>
                        <small class='text-info'>Property Ref: {{$apartment->property_ref}}</small>
                        <small class='text-info mx-1'> Ownership: {{$apartment->ownership}}</small>
                        <ul class="list-group list-group-flush">
                            @if($apartment->pay_frequency_id && $apartment->fee)
                                <li class="list-group-item">
                                    <i class="ri-money-dollar-circle-line text-success"></i> <strong>Payment:</strong><br>
                                    {{ ucfirst(optional($pay_time->firstWhere('id', $apartment->pay_frequency_id))->payment_frequency) }} - ${{ number_format($apartment->fee, 2) }}
                                    @if($apartment->booked_expiry)
                                        <span class="badge bg-danger">
                                            @if(\Carbon\Carbon::parse($apartment->booked_expiry) >= \Carbon\Carbon::today())
                                                <i class="fas fa-calendar-alt"></i> Booked till: 
                                                {{ \Carbon\Carbon::parse($apartment->booked_expiry)->addDay()->format('Y-m-d') }}
                                            @endif
                                        </span>
                                    @endif
                                </li>
                            @endif
                            <!-- Display Amenities with Sizes -->
                            @foreach($apartment->amenities as $amenity)
                                <li class="list-group-item">
                                    <i class="ri-checkbox-circle-line text-success"></i>
                                    {{ $amenity->amenities->name }}:  
                                    <span class="badge rounded-pill bg-success float-end">
                                        {{ $amenity->amenity_number }}
                                        @if($amenity->amenitySizes && $amenity->amenitySizes->isNotEmpty())
                                            ({{ $amenity->amenitySizes->pluck('amenity_size')->implode(', ') }})
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        
                        <!-- Hidden inputs for apartment data -->
                        <input type="hidden" class="apartment_id" value="{{ $apartment->id }}">
                        <input type="hidden" class="pay_time_id" value="{{ $apartment->pay_frequency_id }}">
                        <input type="hidden" class="shelter_id" value="{{ $apartment->shelter_id }}">
                        <input type="hidden" class="block_id" value="{{ $apartment->block_models_id }}">
                        <input type="hidden" class="fee_amt" value="{{ $apartment->fee }}">
                        <input type="hidden" class="block_shelter" value="{{ $blockShelter->id }}">
                          <input type="hidden" class="apartment_address" value="{{$apartment->address}}">
                      
                        <!-- Book Now Button -->
                        <button class="btn btn-success mt-3 w-100 py-2 rounded book-now-btn" aria-label="Book Now">
                            <strong>Book Now</strong>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="row">
        <div class="col-12 float-right">
         {{ $apartments->appends(request()->query())->links('pagination::bootstrap-4') }}

        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const spinner = document.createElement('div');
    spinner.classList.add('spinner-border', 'text-success');
    spinner.setAttribute('role', 'status');
    const spinnerText = document.createElement('span');
    spinnerText.classList.add('visually-hidden');
    spinnerText.innerText = 'Loading...';
    spinner.appendChild(spinnerText);
    const today = new Date().toISOString().split('T')[0];
    const startDateInput = document.getElementById('start-date-input');
    const endDateInput = document.getElementById('end-date-input');

    // Set start date minimum to today and end date minimum to tomorrow
    startDateInput.setAttribute('min', today);
    
    function getNextDay(date) {
        const nextDay = new Date(date);
        nextDay.setDate(nextDay.getDate() + 1);
        return nextDay.toISOString().split('T')[0];
    }

    // Set the initial end date minimum to the next day after today
    endDateInput.setAttribute('min', getNextDay(today));

    startDateInput.addEventListener('change', function() {
        const selectedStartDate = this.value;
        const nextDay = getNextDay(selectedStartDate);

        // Ensure the end date can't be before the minimum allowed end date
        endDateInput.setAttribute('min', nextDay);
    });

    // Handle "Book Now" button click
    document.querySelectorAll('.book-now-btn').forEach(button => {
        button.addEventListener('click', function() {
            const card = this.closest('.card-body');
            const apartmentId = card.querySelector('.apartment_id').value;
            const payTimeId = card.querySelector('.pay_time_id').value;
            const shelterId = card.querySelector('.shelter_id').value;
            const blockId = card.querySelector('.block_id').value;
            const blockShelter = card.querySelector('.block_shelter').value;
            const feeAmt = card.querySelector('.fee_amt').value;
            const apartment_address = card.querySelector('.apartment_address').value;
          
            document.getElementById('bookingModal').querySelector('.modal-content').classList.add('loading-spinner');
            document.getElementById('apartment_id').value = apartmentId;
            document.getElementById('pay_time_id').value = payTimeId;
            document.getElementById('shelter_id').value = shelterId;
            document.getElementById('block_id').value = blockId;
            document.getElementById('block_shelter').value = blockShelter;
            document.getElementById('fee').innerText = parseFloat(feeAmt).toFixed(2);
            document.getElementById('apartment_name').innerText = apartment_address;
            const bookingModalBody = document.querySelector('.modal-body');
            bookingModalBody.prepend(spinner);

            // Show booking modal
            const bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));
            bookingModal.show();

            // Simulate data fetching delay and remove spinner
            setTimeout(() => {
                spinner.remove();
            }, 2000);
        });
    });

    // Handle form submission via Fetch API
    const bookingForm = document.getElementById('bookingForm');
    bookingForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const submitButton = document.getElementById('manage');
        const formData = new FormData(bookingForm);
  
        submitButton.setAttribute('disabled', 'true');

        fetch(bookingForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            spinner.remove();
            submitButton.removeAttribute('disabled');

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Booking Confirmed!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Booking Failed!',
                    text: data.message || 'An error occurred, please try again.',
                });
            }

            const bookingModal = bootstrap.Modal.getInstance(document.getElementById('bookingModal'));
            bookingModal.hide();
        })
        .catch(error => {
            console.error(error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An error occurred while booking. Please try again later.',
            });
            submitButton.removeAttribute('disabled');
        });
    });
});
</script>
@endsection