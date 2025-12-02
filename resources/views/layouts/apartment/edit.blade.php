@extends('layouts.dashboard.landpage')

@section('content')
    <!-- Custom Preloader for AJAX -->
    <div id="ajax-preloader" class="d-none">
        <div class="spinner">
            <i class="ri-loader-line"></i>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between mb-1 w-100">
                <div class="page-title-right w-100">
                    <div class="card border-0 shadow-sm p-2 w-100">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                     <h4 class="card-title mb-3">Estate :
                                        <span class="text-success">Building:  {{ ucwords($blockShelter->block->name) }}</span>
                                         <span class="text-success">Apartment: {{ ucwords($blockShelter->shelter->name) }}</span>
                                    </h4>
                                    
                                    <h4 class="card-title mb-3">Estate Owner:
                                        <span class="text-success">{{ ucwords($blockShelter->estateOwner->fName) }} {{ ucwords($blockShelter->estateOwner->lName) }}</span>
                                    </h4>
                                    <h5 class="text-muted">Contact Details</h5>
                                    <p class="mb-2"><strong>Address:</strong> {{ ucfirst($blockShelter->estateOwner->address) }}</p>
                                    <p class="mb-2"><strong>Phone:</strong> {{ $blockShelter->estateOwner->phones }}</p>
                                    <p class="mb-3"><strong>Email:</strong> {{ $blockShelter->estateOwner->email }}</p>
                                </div>
                                <div class="col-md-6 text-md-end mt-4 mt-md-0">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('property.index') }}" class="text-decoration-none">
                                                <i class="ri-home-4-line"></i> 
</a>
                                        </li>
                                        <li class="breadcrumb-item active"></li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Blocks Container -->
    <div class="row" id="apartments-container">
    @foreach($apartments as $index => $apartment)
        @foreach($apart_info as $apart)
            @if($apartment->id === $apart->apartment_id)
                <div class="col-md-4">
                    <div class="card">
                        <h6 class="card-header text-center">
                            {{ isset($blockShelter->shelter->name) ? ucwords($blockShelter->shelter->name) . ' ' . ($apartments->firstItem() + $index) : 'Apartment ' . ($apartments->firstItem() + $index) }}
                        </h6>
                        <div class="card-body">
                            <h5 class="card-title text-center">Apartment Details</h5>
                            <p class="card-text text-muted">Kindly update the details step-by-step.</p>

                            <!-- Wizard Form -->
                            <form method="POST" action="{{ route('apartment.create', $apartment->id) }}" class="amenities-form wizard-form" data-apartment-id="{{ $apartment->id }}">
                                @csrf
                                <input type="hidden" name="block_id" value="{{ $apartment->block_models_id }}">
                                <input type="hidden" name="shelter_id" value="{{ $apartment->shelter_id }}">
                                <input type="hidden" name="apart_id" value="{{ $apartment->id }}">

                                <!-- Step 1: Payment & Property Details -->
                                <div class="wizard-step" data-step="1">
                                    <h6 class="text-muted mt-3 text-center">Step 1: Payment & Property Details</h6>
                                    <div class="row mb-2 align-items-center">
                                        <div class="col-6">
                                            <label for="pay_freq_name_{{ $apartment->id }}" class="form-label">Frequency</label>
                                            <select class="form-select" id="pay_freq_name_{{ $apartment->id }}" name="pay_freq_id" required>
                                                <option value="">Select frequency</option>
                                                @foreach($pay_time as $pay_t)
                                                    <option value="{{ $pay_t->id }}" {{ $apartment->pay_frequency_id == $pay_t->id ? 'selected' : '' }}>
                                                        {{ ucfirst($pay_t->payment_frequency) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label for="pay_fee_{{ $apartment->id }}" class="form-label">Fee</label>
                                            <input type="number" class="form-control" id="pay_fee_{{ $apartment->id }}" name="fee" value="{{ $apartment->fee ?? old('fee') }}" placeholder="Fee" min="1" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label for="tenancy_type_{{ $apartment->id }}" class="form-label">Tenancy Type</label>
                                            <select class="form-select" id="tenancy_type_{{ $apartment->id }}" name="tenancy_type" required>
                                                <option value="">Select Tenancy Type</option>
                                                @foreach($tenancy_type as $type)
                                                    <option value="{{ $type->name }}" {{ $apart->tenancy_type == $type->name ? 'selected' : '' }}>{{ ucfirst($type->name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="property_score_code_{{ $apartment->id }}" class="form-label">Property Score</label>
                                            <input type="text" id="property_score_code_{{ $apartment->id }}" name="pro_sco_code" placeholder="Property score" class="form-control" value="{{ $apart->pro_sco_code ?? old('pro_sco_code') }}">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="ownership_{{ $apartment->id }}" class="form-label">Ownership</label>
                                            <input type="text" id="ownership_{{ $apartment->id }}" name="ownership" placeholder="Ownership" class="form-control" value="{{ $apart->ownership ?? old('ownership') }}">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="admin_unit_{{ $apartment->id }}" class="form-label">Admin Unit</label>
                                            <input type="text" id="admin_unit_{{ $apartment->id }}" name="admin_unit" placeholder="Admin unit" class="form-control" value="{{ $apart->admin_unit ?? old('admin_unit') }}">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="post_code_{{ $apartment->id }}" class="form-label">Post Code</label>
                                            <input type="text" id="post_code_{{ $apartment->id }}" name="post_code" placeholder="Post Code" class="form-control" value="{{ $apart->post_code ?? old('post_code') }}">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="property_ref_{{ $apartment->id }}" class="form-label">Property Ref</label>
                                            <input type="text" id="property_ref_{{ $apartment->id }}" name="property_ref" placeholder="Property Ref" class="form-control" value="{{ $apart->property_ref ?? old('property_ref') }}">
                                        </div>
                                        <div class="col-md-12 mb-2">
                                            <label for="unit_name_{{ $apartment->id }}" class="form-label">Unit Name</label>
                                            <input type="text" id="unit_name_{{ $apartment->id }}" name="unit_name" placeholder="Unit Name" class="form-control" value="{{ $apart->unit_name ?? old('unit_name') }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 2: Amenities -->
                                <div class="wizard-step" data-step="2" style="display: none;">
                                    <h6 class="text-muted mt-3 text-center">Step 2: Amenities</h6>
                                    <div class="amenities-container">
                                        @foreach($amenity_apartment as $amen)
                                            @php
                                                $isApartmentMatch = $amen['amenity_apart_id'] === $apartment->id;
                                            @endphp

                                            @if($isApartmentMatch)
                                                @php
                                                    // Filter amenities that match the apartment ID and amenity ID
                                                    $matchingAmenities = collect($amen['amenity_sizes'])->filter(fn($info) => 
                                                        $info['apartment_id'] === $apartment->id && 
                                                        $info['amenity_id'] === $amen['amenity_id']
                                                    );

                                                    // Ensure the array has a zero-based index
                                                    $matchingAmenitiesArray = array_values($matchingAmenities->toArray());

                                                    // Count only those with size > 1
                                                    $amenityCount = collect($matchingAmenitiesArray)->filter(fn($info) => $info['size'] > 1)->count();
                                                    
                                                    // If no size > 1, set to zero for those with size <= 1
                                                    if ($amenityCount === 0) {
                                                        $amenityCount = 0;
                                                    }
                                                @endphp

                                                <div class="row mb-2 align-items-center amenity-group">
                                                    <div class="col-6">
                                                        <label for="amenity_{{ $amen['amenity_id'] }}_{{ $apartment->id }}" class="form-label mb-0">
                                                            {{ ucfirst($amen['amenity_name']) }}
                                                        </label>
                                                        <input type="hidden" name="amenities[{{ $amen['amenity_id'] }}][id]" value="{{ $amen['amenity_id'] }}">
                                                    </div>
                                                    <div class="col-6 d-flex">
                                                        <input type="number" class="form-control amenity"
                                                               id="amenity_{{ $amen['amenity_id'] }}_{{ $apartment->id }}"
                                                               name="amenities[{{ $amen['amenity_id'] }}][quantity]"
                                                               value="{{ $amenityCount }}"
                                                               min="0" placeholder="Qty"
                                                               data-amenity-id="{{ $amen['amenity_id'] }}"
                                                               data-apartment-id="{{ $apartment->id }}"
                                                               data-amenity-name="{{ $amen['amenity_name'] }}" required readonly>
                                                        <i class="fas fa-pen load_modal btn bg-light"  
                                                           data-amenity-id="{{ $amen['amenity_id'] }}"
                                                           data-apartment-id="{{ $apartment->id }}"
                                                           data-amenity-name="{{ $amen['amenity_name'] }}"
                                                           data-matching-amenities="{{ json_encode($matchingAmenitiesArray) }}"
                                                           data-matching-amenities-count="{{ $amenityCount }}"
                                                           data-shelter-id="{{ $apartment->shelter_id}}"></i>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Navigation Buttons -->
                                <div class="d-flex justify-content-between mt-3">
                                    <button type="button" class="btn btn-secondary wizard-prev" style="display: none;"><i class="fa fa-arrow-left"></i> Previous</button>
                                    <button type="button" class="btn btn-info wizard-next">Next <i class="fa fa-arrow-right"></i></button>
                                    <button type="submit" class="btn btn-success wizard-submit" style="display: none;">Update <i class="fa fa-arrow-circle-up"></i></button>
                                </div>
                            </form>
                        </div>
                    </div> 
                </div>
            @endif
        @endforeach
    @endforeach
</div>

<!-- Pagination Links -->
@if($apartments->hasPages())
    <div class="d-flex justify-content-end mt-3">
        {{ $apartments->links('pagination::bootstrap-4') }}
    </div>
@endif


    <!-- Modal for Amenity Sizes -->
    <div class="modal fade" id="amenitySizeModal" tabindex="-1" aria-labelledby="amenitySizeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-light">
                <div class="modal-header">
                    <h5 class="modal-title" id="amenitySizeModalLabel">Enter Amenity Sizes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="amenitySizeForm" method="POST" action="{{route('amenity.size.update')}}">
                    @csrf
                    <div class="modal-body" id="amenitySizeInputs">
                        <!-- Dynamic inputs will be added here -->
                    </div>
                    <input type="hidden" name="apartment_id" id="modal-apartment-id">
                    <input type="hidden" name="shelter_id" id="modal-shelter-id">
                    <input type="hidden" name="amenity_id" id="modal-amenity-id">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit Sizes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <style>
        #ajax-preloader {
            position: fixed;
            inset: 0;
            background-color: rgba(40, 167, 69, 0.8);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        #ajax-preloader.d-none { display: none; }
        .spinner { font-size: 36px; color: white; animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const preloader = document.getElementById('ajax-preloader');
        
        // Check if preloader exists
        if (preloader) {
            preloader.classList.add('d-none');
        }

        // Wizard Navigation and Form Submission
        document.querySelectorAll('.wizard-form').forEach(form => {
            const steps = form.querySelectorAll('.wizard-step');
            const prevBtn = form.querySelector('.wizard-prev');
            const nextBtn = form.querySelector('.wizard-next');
            const submitBtn = form.querySelector('.wizard-submit');
            let currentStep = 1;

            const updateStep = () => {
                steps.forEach((step, index) => {
                    step.style.display = index + 1 === currentStep ? 'block' : 'none';
                });

                prevBtn.style.display = currentStep === 1 ? 'none' : 'inline-block';
                nextBtn.style.display = currentStep === steps.length ? 'none' : 'inline-block';
                submitBtn.style.display = currentStep === steps.length ? 'inline-block' : 'none';
            };

            prevBtn.addEventListener('click', () => {
                if (currentStep > 1) {
                    currentStep--;
                    updateStep();
                }
            });

            nextBtn.addEventListener('click', () => {
                if (currentStep < steps.length) {
                    currentStep++;
                    updateStep();
                }
            });

            updateStep();

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                form.querySelector('.error-container')?.remove();
                if (preloader) preloader.classList.remove('d-none');

                const formData = new FormData(form);

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-Token': csrfToken
                        }
                    });

                    const data = await response.json();
                    if (preloader) preloader.classList.add('d-none');

                    if (data.errors) {
                        form.insertAdjacentHTML(
                            'beforeend',
                            `<div class="error-container alert alert-danger">${Object.values(data.errors).flat().join('<br>')}</div>`
                        );
                    } else {
                        Swal.fire({
                            position: 'top-end',
                            icon: data.success ? 'success' : 'warning',
                            title: data.success ? 'Updated successfully' : 'Something went wrong',
                            showConfirmButton: false,
                            timer: 1500
                        });
                   
                    }
                } catch (error) {
                    if (preloader) preloader.classList.add('d-none');
                    console.error('Error:', error);

                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: 'An error occurred',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        });

        // Amenity Size Modal Handler
        document.getElementById('amenitySizeForm')?.addEventListener('submit', 
        async (e) => {
            e.preventDefault();
            if (preloader) preloader.classList.remove('d-none');

            const formData = new FormData(e.target);

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch(e.target.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-Token': csrfToken
                    }
                });

                const data = await response.json();
                if (preloader) preloader.classList.add('d-none');

                Swal.fire({
                    position: 'top-end',
                    icon: data.errors ? 'error' : data.success ? 'success' : 'warning',
                    title: data.errors ? 'Validation Failed' : data.success ? 'Sizes saved successfully' : 'Something went wrong',
                    html: data.errors ? Object.values(data.errors).flat().join('<br>') : '',
                    timer: 2000
                });

                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('amenitySizeModal'))?.hide();
                    if(data.count_amenity_modal){
                        window.location.reload(true);
                        
                    }
                }
            } catch (error) {
                if (preloader) preloader.classList.add('d-none');
                console.error('Error:', error);

                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: 'An error occurred',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        }
        );

        // Load Modal for Amenities
        document.body.addEventListener('click', (e) => {
            const element = e.target.closest('.load_modal');
            if (!element) return;

            // Extract data attributes
            const apartmentId = element.dataset.apartmentId;
            const amenityId = element.dataset.amenityId;
            const amenityName = element.dataset.amenityName;
            const matchingAmenitiesCount = parseInt(element.dataset.matchingAmenitiesCount || '0', 10);
             const shelterId = element.dataset.shelterId;

            // Parse matching amenities safely
            let amenities = [];
            try {
                amenities = JSON.parse(element.dataset.matchingAmenities || '[]');
            } catch (error) {
                console.error('Error parsing amenities:', error);
            }

            console.log({ apartmentId, amenityId, amenityName, amenities, matchingAmenitiesCount, shelterId });

            // Ensure elements exist before setting values
            document.getElementById('modal-apartment-id')?.setAttribute('value', apartmentId);
            document.getElementById('modal-amenity-id')?.setAttribute('value', amenityId);
             document.getElementById('modal-shelter-id')?.setAttribute('value', shelterId);

            // Generate input fields for amenity sizes
            const modalBody = document.getElementById('amenitySizeInputs');
            modalBody.innerHTML = '';

            let inputCount = amenities.length > 0 ? amenities.length : 1;
            let inputCounter = inputCount;

            for (let i = 0; i < inputCount; i++) {
                const sizeValue = amenities[i]?.size || ''; // Use existing value if available
                const sizeId = amenities[i]?.amenity_size_id|| '';
                const inputWrapper = document.createElement('div');
                inputWrapper.className = 'mb-3 d-flex align-items-center';
                inputWrapper.innerHTML = `
                    <label for="amenity_size_${i}" class="form-label flex-grow-1">${titleCase(amenityName)} ${i + 1} Size</label>
                    <input type="number" step="0.01" class="form-control me-2" id="amenity_size_${i}" name="amenity_sizes[${i}]" value="${sizeValue}" min="1" required>
                    <input type="number" class="form-control me-2" id="amenity_size_${i}" name="amenity_size_id[${i}]" value="${sizeId}" min="1" required>
                `;

                modalBody.appendChild(inputWrapper);
            }

            // Add a plus button to allow dynamic input addition
            const addButton = document.createElement('button');
            addButton.className = 'btn btn-primary mt-2';
            addButton.innerHTML = `<i class="fa fa-plus"></i> Add Size`;
            addButton.type = 'button';

            modalBody.appendChild(addButton);

            // Event Listener for Adding Input Fields
            addButton.addEventListener('click', () => {
                const newInputWrapper = document.createElement('div');
                newInputWrapper.className = 'mb-3 d-flex align-items-center';
                newInputWrapper.innerHTML = `
                    <label for="amenity_size_${inputCounter}" class="form-label flex-grow-1">${titleCase(amenityName)} ${inputCounter + 1} Size</label>
                    <input type="number" class="form-control me-2" id="amenity_size_${inputCounter}" name="amenity_sizes[${inputCounter}]" min="1" required>
                    <button type="button" class="btn btn-danger remove-input"><i class="fa fa-times"></i></button>
                `;

                modalBody.insertBefore(newInputWrapper, addButton);
                inputCounter++;
            });

            // Event Listener for Removing Input Fields (Delegation)
            modalBody.addEventListener('click', (e) => {
                if (e.target.closest('.remove-input')) {
                   inputCounter--;
                    e.target.closest('.mb-3').remove();
                }
            });

            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('amenitySizeModal'));
            modal.show();
        });

        // Function to convert text to Title Case
        function titleCase(str) {
            return str.replace(/\w\S*/g, (txt) => txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase());
        }
    });
</script>


@endsection