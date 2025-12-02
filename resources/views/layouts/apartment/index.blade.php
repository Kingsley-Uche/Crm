@extends('layouts.dashboard.landpage')

@section('content')
    <!-- Custom Preloader for AJAX -->
    <div id="ajax-preloader">
        <div class="spinner">
            <i class="ri-loader-line"></i>
        </div>
    </div>
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between mb-3">
            <div class="w-100">
                <div class="card border-0 shadow-sm p-3">
                    <div class="row align-items-start">
                        <div class="col-md-6">
                            <h4 class="card-title mb-2">Estate Information</h4>
                            <p class="mb-1">
                                <strong class="text-success">Building Title:</strong> {{ ucwords($blockShelter->block->name) }}
                            </p>
                            <p class="mb-1">
                                <strong class="text-dark">Owner:</strong> 
                                {{ ucwords($blockShelter->estateOwner->fName) }} 
                                {{ ucwords($blockShelter->estateOwner->lName) }}
                            </p>

                            <h5 class="text-muted mt-4">Contact Details</h5>
                            <p class="mb-1"><strong>Address:</strong> {{ $blockShelter->estateOwner->address }}</p>
                            <p class="mb-1"><strong>Phone:</strong> {{ $blockShelter->estateOwner->phones }}</p>
                            <p class="mb-0"><strong>Email:</strong> {{ $blockShelter->estateOwner->email }}</p>
                        </div>

                        <div class="col-md-6 text-md-end mt-4 mt-md-0">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('property.index') }}" class="text-decoration-none">
                                            <i class="ri-home-4-line"></i> Buildings
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        Apartment: {{ ucwords($blockShelter->shelter->name) }}
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <p class="text-center text-danger small mb-0">
                                ** Kindly complete the details of all apartments **
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


    <!-- Blocks Container伐伐 -->
    <div class="row">
        @foreach($apartments as $index => $apartment)
            @php
                $patterns = [
                    '/(\d+)\s*,\s*([\w\s]+)\s*,/', // "26, Block Name, ..."
                    '/(\d+)\s+([\w\s]+),\s*[\w\s]+,\s*[a-zA-Z0-9\s]+/', // "130 trinity gardens, london, e16 4qb"
                    '/(\d+)\s+([\w\s]+)/', // Fallback: "26 Block Name"
                ];

                $apartmentNumber = null;
                $apartmentType = 'Unknown';

                foreach ($patterns as $pattern) {
                    if (preg_match($pattern, $apartment->address, $matches)) {
                        $apartmentNumber = (int)$matches[1];
                        $apartmentType = ($apartmentNumber % 2 === 0) ? 'Even' : 'Odd';
                        break;
                    }
                }
            @endphp
            <div class="col-md-4">
                <div class="card">
                    <h6 class="card-header text-center">{{ ucwords($blockShelter->shelter->name).' '. ($apartments->firstItem() + $index) }}</h6>
                    <div class="card-body">
                        <h5 class="card-title text-center">Apartment Details</h5>
                        <small class="badge bg-warning m">{{ $apartmentType }}</small>
                        <span class="text-info"></span> <b>Address:</b> {{ ucwords( $apartment->address) }}</span>
                        <p class="card-text text-muted">Kindly enter the details step-by-step.</p>

                        <!-- Wizard Form -->
                        <form method="POST" action="{{ route('apartment.create') }}" class="amenities-form wizard-form" data-apartment-id="{{ $apartment->id }}">
                            @csrf
                            <!-- Hidden Inputs -->
                            <input type="hidden" name="block_id" value="{{ $apartment->block_models_id }}">
                            <input type="hidden" name="shelter_id" value="{{ $apartment->shelter_id }}">
                            <input type="hidden" name="apart_id" value="{{ $apartment->id }}">

                            <!-- Wizard Steps -->
                            <div class="wizard-step" data-step="1">
                                <h6 class="text-muted mt-3 text-center">Step 1: Payment & Property Details</h6>
                                <!-- Payment Section -->
                                <div class="row mb-2 align-items-center">
                                    <div class="col-6">
                                        <label for="pay_freq_name_{{ $apartment->id }}" class="form-label">Payment Frequency</label>
                                        <select class="form-select" id="pay_freq_name_{{ $apartment->id }}" name="pay_freq_id" required>
                                            <option value="">Select frequency</option>
                                            @foreach($pay_time as $pay_t)
                                                <option value="{{ $pay_t->id }}" {{ $apartment->pay_freq_id == $pay_t->id ? 'selected' : '' }}>
                                                    {{ ucfirst($pay_t->payment_frequency) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label for="pay_fee_{{ $apartment->id }}" class="form-label">Fee (£)</label>
                                        <input type="number" 
                                               class="form-control" 
                                               id="pay_fee_{{ $apartment->id }}" 
                                               name="fee" 
                                               value="{{ $apartment->fee ?? '' }}"
                                               placeholder="Enter fee amount" 
                                               min="1" 
                                               step="0.01"
                                               required>
                                    </div>
                                </div>
                                <!-- Tenancy & Property Details -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="tenancy_type_{{ $apartment->id }}" class="form-label">Tenancy Type</label>
                                        <select class="form-select" id="tenancy_type_{{ $apartment->id }}" name="tenancy_type" required>
                                            <option value="">Select Tenancy Type</option>
                                            @foreach($tenancy_type as $type)
                                                <option value="{{ $type->name }}" {{ $apartment->tenancy_type == $type->name ? 'selected' : '' }}>
                                                    {{ ucfirst($type->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="property_score_code_{{ $apartment->id }}" class="form-label">Property Score</label>
                                        <input type="text" 
                                               id="property_score_code_{{ $apartment->id }}" 
                                               name="pro_sco_code" 
                                               placeholder="Property score" 
                                               class="form-control" 
                                               value="{{ $apartment->pro_sco_code ?? '' }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="ownership_{{ $apartment->id }}" class="form-label">Ownership</label>
                                        <input type="text" 
                                               id="ownership_{{ $apartment->id }}" 
                                               name="ownership" 
                                               placeholder="Ownership details" 
                                               class="form-control" 
                                               value="{{ $apartment->ownership ?? '' }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="admin_unit_{{ $apartment->id }}" class="form-label">Admin Unit</label>
                                        <input type="text" 
                                               id="admin_unit_{{ $apartment->id }}" 
                                               name="admin_unit" 
                                               placeholder="Administrative unit" 
                                               class="form-control" 
                                               value="{{ $apartment->admin_unit ?? '' }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="post_code_{{ $apartment->id }}" class="form-label">Post Code</label>
                                        <input type="text" 
                                               id="post_code_{{ $apartment->id }}" 
                                               name="post_code" 
                                               placeholder="Postal code" 
                                               class="form-control" 
                                               value="{{ $apartment->post_code ?? '' }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="property_ref_{{ $apartment->id }}" class="form-label">Property Reference</label>
                                        <input type="text" 
                                               id="property_ref_{{ $apartment->id }}" 
                                               name="property_ref" 
                                               placeholder="Property reference" 
                                               class="form-control" 
                                               value="{{ $apartment->property_ref ?? '' }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="unit_name_{{ $apartment->id }}" class="form-label">Address</label>
                                        <input type="text" 
                                               id="unit_name_{{ $apartment->id }}" 
                                               name="unit_name" 
                                               placeholder="Unique unit name" 
                                               class="form-control" 
                                               value="{{ $apartment->address ?? '' }}"
                                               required>
                                    </div>
                                </div>
                            </div>

                            <div class="wizard-step" data-step="2" style="display: none;">
                                <h6 class="text-muted mt-3 text-center">Step 2: Amenities</h6>
                                <div class="amenities-container">
                                    @foreach($amenities as $amenity)
                                        @php
                                            $amenityData = $amenity_apartment->firstWhere('amenity_id', $amenity->id);
                                        @endphp
                                        <div class="row mb-2 align-items-center amenity-group">
                                            <div class="col-6">
                                                <label for="amenity_{{ $amenity->id }}_{{ $apartment->id }}" class="form-label mb-0">
                                                    {{ ucfirst($amenity->name) }}
                                                </label>
                                                <input type="hidden" name="amenities[{{ $amenity->id }}][id]" value="{{ $amenity->id }}">
                                            </div>
                                            <div class="col-6">
                                                <input type="number"
                                                       class="form-control"
                                                       id="amenity_{{ $amenity->id }}_{{ $apartment->id }}"
                                                       name="amenities[{{ $amenity->id }}][quantity]"
                                                       value="{{ $amenityData->amenity_number ?? 0 }}"
                                                       min="0"
                                                       data-amenity-id="{{ $amenity->id }}"
                                                       placeholder="Quantity"
                                                       required>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Navigation Buttons -->
                            <div class="d-flex justify-content-between mt-3">
                                <button type="button" class="btn btn-secondary wizard-prev" style="display: none;">
                                    <i class="fa fa-arrow-circle-left text-white"></i> Back
                                </button>
                                <button type="button" class="btn btn-info wizard-next">
                                    Next <i class="fa fa-arrow-circle-right text-white"></i>
                                </button>
                                <button type="submit" class="btn btn-success wizard-submit" style="display: none;">
                                    Update <i class="fa fa-arrow-circle-up text-white"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($apartments && $apartments->hasPages())
        <div class="d-flex justify-content-end mt-3">
            {{ $apartments->links('pagination::bootstrap-4') }}
        </div>
    @endif
@endsection

@section('scripts')
    <style>
        #ajax-preloader {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(40, 167, 69, 0.8);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
        }
        .spinner {
            font-size: 36px;
            color: white;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Wizard Navigation and Form Handling
            document.querySelectorAll('.wizard-form').forEach(form => {
                const steps = form.querySelectorAll('.wizard-step');
                const prevBtn = form.querySelector('.wizard-prev');
                const nextBtn = form.querySelector('.wizard-next');
                const submitBtn = form.querySelector('.wizard-submit');
                let currentStep = 1;

                function updateStep() {
                    steps.forEach((step, index) => {
                        step.style.display = (index + 1 === currentStep) ? 'block' : 'none';
                    });
                    prevBtn.style.display = currentStep === 1 ? 'none' : 'inline-block';
                    nextBtn.style.display = currentStep === steps.length ? 'none' : 'inline-block';
                    submitBtn.style.display = currentStep === steps.length ? 'inline-block' : 'none';
                }

                prevBtn.addEventListener('click', () => {
                    if (currentStep > 1) {
                        currentStep--;
                        updateStep();
                    }
                });

                nextBtn.addEventListener('click', () => {
                    // Validate current step before moving forward
                    const currentInputs = steps[currentStep - 1].querySelectorAll('input[required], select[required]');
                    let isValid = true;
                    currentInputs.forEach(input => {
                        if (!input.value) {
                            input.classList.add('is-invalid');
                            isValid = false;
                        } else {
                            input.classList.remove('is-invalid');
                        }
                    });

                    if (isValid && currentStep < steps.length) {
                        currentStep++;
                        updateStep();
                    } else if (!isValid) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Missing Fields',
                            text: 'Please fill all required fields before proceeding.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });

                // AJAX Form Submission
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Validate all required fields in the final step
                    const allInputs = form.querySelectorAll('input[required], select[required]');
                    let isValid = true;
                    allInputs.forEach(input => {
                        if (!input.value) {
                            input.classList.add('is-invalid');
                            isValid = false;
                        } else {
                            input.classList.remove('is-invalid');
                        }
                    });

                    if (!isValid) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Missing Fields',
                            text: 'Please fill all required fields.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        return;
                    }

                    document.getElementById('ajax-preloader').style.display = 'flex';
                    const formData = new FormData(form);

                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('ajax-preloader').style.display = 'none';
                        if (data.success) {
                            Swal.fire({
                                position: "top-end",
                                icon: "success",
                                title: "Updated successfully",
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else if (data.errors) {
                            let errorMsg = Object.values(data.errors).flat().join(', ');
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: errorMsg,
                                timer: 3000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Something went wrong',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    })
                    .catch(error => {
                        document.getElementById('ajax-preloader').style.display = 'none';
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'An error occurred',
                            text: 'Please try again later.',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    });
                });

                updateStep(); // Initialize wizard
            });

            // Dynamic Size Inputs
            document.querySelectorAll(".amenities-form").forEach((form) => {
                form.addEventListener("input", function (event) {
                    if (event.target.matches("input[type='number'][name^='amenities']")) {
                        const quantityInput = event.target;
                        const quantity = parseInt(quantityInput.value) || 0;
                        const amenityId = quantityInput.getAttribute("data-amenity-id");
                        const amenitiesContainer = quantityInput.closest(".amenities-container");
                        const sizeContainerId = `size-container-${amenityId}-${form.dataset.apartmentId}`;
                        const existingSizeContainer = document.getElementById(sizeContainerId);

                        if (existingSizeContainer) {
                            existingSizeContainer.remove();
                        }

                        if (quantity > 0) {
                            const sizeContainer = document.createElement("div");
                            sizeContainer.id = sizeContainerId;
                            sizeContainer.classList.add("mt-2");

                            for (let i = 1; i <= quantity; i++) {
                                const sizeInputGroup = document.createElement("div");
                                sizeInputGroup.classList.add("row", "mb-2", "align-items-center");
                                sizeInputGroup.innerHTML = `
                                    <div class="col-6">
                                        <label for="amenity_size_${amenityId}_${i}_${form.dataset.apartmentId}" class="form-label">
                                            Size of ${quantityInput.closest('.amenity-group').querySelector('label').textContent.trim()} ${i}
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <input type="text" class="form-control" 
                                               id="amenity_size_${amenityId}_${i}_${form.dataset.apartmentId}" 
                                               name="amenities[${amenityId}][sizes][]" 
                                               placeholder="Enter size" 
                                               required>
                                    </div>
                                `;
                                sizeContainer.appendChild(sizeInputGroup);
                            }
                            amenitiesContainer.appendChild(sizeContainer);
                        }
                    }
                });
            });
        });
    </script>
@endsection