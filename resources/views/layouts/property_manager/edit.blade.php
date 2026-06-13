@extends('layouts.dashboard.landpage')

@section('content')
    <style>
        .apartment-card { 
            border: 1px solid #e9ecef; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            padding: 20px;
        }
        .amenity-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
        }
        .amenity-item label {
            flex: 1;
            margin-bottom: 0;
            font-weight: 500;
        }

        /* Custom Searchable Dropdown */
        .custom-select-wrapper { 
            position: relative; 
            width: 100%; 
        }
        .custom-select-search {
            width: 100%; 
            padding: 10px 12px; 
            border: 1px solid #ced4da; 
            border-radius: 4px;
            cursor: pointer;
            background: white;
        }
        .custom-select-dropdown {
            position: absolute; 
            top: 100%; 
            left: 0; 
            right: 0; 
            border: 1px solid #ced4da;
            background: white; 
            max-height: 240px; 
            overflow-y: auto; 
            z-index: 1050;
            display: none; 
            border-radius: 4px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.18);
        }
        .custom-select-option { 
            padding: 10px 12px; 
            cursor: pointer;
        }
        .custom-select-option:hover { 
            background: #f8f9fa; 
        }
        .custom-select-option.selected {
            background: #28a745;
            color: white;
        }
        .custom-select-wrapper.open .custom-select-dropdown { 
            display: block; 
        }
        .invalid-feedback { 
            display: none; 
            color: #dc3545; 
            font-size: 0.875em; 
        }
        .is-invalid { 
            border-color: #dc3545; 
        }
    </style>

    <!-- Preloader -->
    <div id="custom-preloader">
        <div class="spinner"><i class="ri-loader-line"></i></div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Property Profile Manager</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="#">Property</a></li>
                        <li class="breadcrumb-item active">Edit Apartments</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    
                    <div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <h4 class="card-title mb-0">
            Editing Apartments —
            Location:
            <strong>{{ $locations->firstWhere('id', $location_id)->name ?? 'N/A' }}</strong>
            |
            Shelter:
            <strong>{{ $shelters->firstWhere('id', $shelter_id)->name ?? 'N/A' }}</strong>
        </h4>
    </div>

    <div class="col-md-4 text-md-end mt-2 mt-md-0">
        <a href="{{ route('property.create') }}" class="btn btn-success">
            <i class="ri-add-line me-1"></i>
            Create Apartment
        </a>
    </div>
</div>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        @forelse ($apartments as $apartment)
                            <div class="col-md-6 col-lg-4">
                                <div class="apartment-card">
                                    <h5 class="mb-3">Apartment #{{ $apartment->id }}</h5>

                                    
                                        <form class="ajax-apartment-form" method="POST" action="{{ route('property.apartment.update', $apartment->id) }}"
>
                                        @csrf
                                        @method('PUT')

                                        <input type="hidden" name="location_id" value="{{ $location_id }}">
                                        <input type="hidden" name="shelter_id" value="{{ $shelter_id }}">

                                        <!-- Unit Number -->
                                        <div class="mb-3">
                                            <label class="form-label">Unit Number</label>
                                            <input type="text" name="unit_number" class="form-control" 
                                                   value="{{ old('unit_number', $apartment->unit_number) }}">
                                        </div>

                                        <!-- Property Reference -->
                                        <div class="mb-3">
                                            <label class="form-label">Property Reference</label>
                                            <input type="text" name="property_ref" class="form-control" 
                                                   value="{{ old('property_ref', $apartment->property_ref) }}">
                                        </div>

                                        <!-- Address -->
                                        <div class="mb-3">
                                            <label class="form-label">Address</label>
                                            <input type="text" name="address" class="form-control" 
                                                   value="{{ old('address', $apartment->address) }}">
                                        </div>

                                        <!-- Custom Searchable Landlord Dropdown -->
                                        
                                            <div class="mb-3">
    <label class="form-label">Landlord</label>

    <select
        name="landlord_id"
        class="form-control js-searchable">

        <option value="">Select Landlord</option>

        @foreach($landlords as $landlord)
            <option
                value="{{ $landlord->id }}"
                {{ $apartment->landlord_id == $landlord->id ? 'selected' : '' }}>

                {{ trim(($landlord->fName ?? '') . ' ' . ($landlord->lName ?? '')) }}
                @if(!empty($landlord->email))
                    - {{ $landlord->email }}
                @endif

            </option>
        @endforeach

    </select>
</div>

                                        <!-- Editable Amenities -->
                                        <div class="mb-4">
                                            <label class="form-label">Amenities</label>
                                            <div class="border p-3 rounded bg-light">
                                                @forelse ($apartment->shelterAmenities as $amenity)
                                                    <div class="amenity-item">
                                                        <label>{{ ucwords($amenity->amenities->name ?? 'Unknown Amenity') }}</label>
                                                        <input type="number" min="0"
                                                               name="amenities[{{ $amenity->id }}]" 
                                                               class="form-control form-control-sm text-end" 
                                                               style="width: 130px;"
                                                               value="{{ old('amenities.'.$amenity->id, $amenity->amenity_number) }}">
                                                    </div>
                                                @empty
                                                    <p class="text-muted small">No amenities found for this apartment.</p>
                                                @endforelse
                                            </div>
                                        </div>

                                        <div class="d-flex gap-2 mt-3">

    <button
        type="submit"
        class="btn btn-success flex-fill save-apartment-btn"
        title="Save Changes">
        <i class="ri-save-line text-white"></i>
    </button>

    <button
        type="button"
        class="btn btn-danger delete-apartment-btn"
        data-id="{{ $apartment->id }}"
        data-url="{{ route('property.apartment.destroy', $apartment->id) }}"
        title="Delete Apartment">
        <i class="ri-delete-bin-line text-white"></i>
    </button>

</div>
                                    </form>
                                </div>
                               
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info text-center">No apartments found in this location/shelter.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ========================================
       AJAX SAVE APARTMENT
    ======================================== */
    document.addEventListener('submit', async function (e) {

        const form = e.target;

        if (!form.matches('.ajax-apartment-form')) {
            return;
        }

        e.preventDefault();

        const submitBtn = form.querySelector('.save-apartment-btn');
        const originalText = submitBtn.innerHTML;

        try {

            submitBtn.disabled = true;
            submitBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm"></span> Saving...';

            const formData = new FormData(form);

            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (!response.ok) {

                let errors = '';

                if (result.errors) {
                    Object.values(result.errors).forEach(items => {
                        errors += items.join('<br>') + '<br>';
                    });
                } else {
                    errors = result.message || 'Validation failed';
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: errors
                });

                return;
            }

            Swal.fire({
                icon: 'success',
                title: 'Saved',
                text: result.message || 'Apartment updated successfully'
            });

        } catch (error) {

            console.error(error);

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Unable to save apartment.'
            });

        } finally {

            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }

    });

    /* ========================================
       DELETE APARTMENT
    ======================================== */
    document.addEventListener('click', async function (e) {

        if (!e.target.classList.contains('delete-apartment-btn')) {
            return;
        }

        const button = e.target;

        const apartmentId = button.dataset.id;
        const deleteUrl = button.dataset.url;

        const confirmDelete = await Swal.fire({
            title: 'Delete Apartment?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete'
        });

        if (!confirmDelete.isConfirmed) {
            return;
        }

        try {

            button.disabled = true;

            const response = await fetch(deleteUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN':
                        document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Delete failed');
            }

            const card = button.closest('.col-md-6');

            if (card) {
                card.remove();
            }

            Swal.fire({
                icon: 'success',
                title: 'Deleted',
                text: result.message || 'Apartment deleted'
            });

        } catch (error) {

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message
            });

        } finally {

            button.disabled = false;
        }

    });

});
</script>
@endsection