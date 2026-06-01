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
                    <h4 class="card-title mb-4">
                        Editing Apartments — 
                        Location: <strong>{{ $locations->firstWhere('id', $location_id)->name ?? 'N/A' }}</strong> | 
                        Shelter: <strong>{{ $shelters->firstWhere('id', $shelter_id)->name ?? 'N/A' }}</strong>
                    </h4>

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

                                    <form method="POST" action="{{ route('property.apartment.update', $apartment->id) }}">
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
                                            <div class="custom-select-wrapper">
                                                <input type="text" 
                                                       class="custom-select-search" 
                                                       placeholder="Search landlord..." 
                                                       readonly>
                                                <input type="hidden" 
                                                       name="landlord_id" 
                                                       class="landlord-hidden"
                                                       value="{{ $apartment->landlord_id }}">
                                                <div class="custom-select-dropdown">
                                                    <div class="custom-select-option" data-value="">None</div>
                                                    @foreach ($landlords as $landlord)
                                                        <div class="custom-select-option" 
                                                             data-value="{{ $landlord->id }}"
                                                             {{ $apartment->landlord_id == $landlord->id ? 'data-selected="true"' : '' }}>
                                                            {{ trim($landlord->fName ?? '' . ' ' . $landlord->lName ?? '') }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
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

                                        <button type="submit" class="btn btn-success w-100">
                                            Save Changes
                                        </button>
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
        document.addEventListener('DOMContentLoaded', () => {

            const wrappers = document.querySelectorAll('.custom-select-wrapper');

            wrappers.forEach(wrapper => {
                const searchInput = wrapper.querySelector('.custom-select-search');
                const hiddenInput = wrapper.querySelector('input[type="hidden"]');
                const dropdown = wrapper.querySelector('.custom-select-dropdown');
                const options = wrapper.querySelectorAll('.custom-select-option');

                // Set initial selected value
                const selected = Array.from(options).find(opt => opt.dataset.value === hiddenInput.value);
                if (selected) {
                    searchInput.value = selected.textContent.trim();
                } else {
                    searchInput.value = "None";
                }

                // Click to toggle dropdown
                searchInput.addEventListener('click', () => {
                    wrapper.classList.toggle('open');
                });

                // Live search filter
                searchInput.addEventListener('input', () => {
                    const filter = searchInput.value.toLowerCase().trim();
                    options.forEach(option => {
                        const text = option.textContent.toLowerCase();
                        option.style.display = text.includes(filter) ? '' : 'none';
                    });
                });

                // Option selection
                options.forEach(option => {
                    option.addEventListener('click', () => {
                        searchInput.value = option.textContent.trim();
                        hiddenInput.value = option.dataset.value;
                        wrapper.classList.remove('open');

                        options.forEach(opt => opt.classList.remove('selected'));
                        option.classList.add('selected');
                    });
                });
            });

            // Close dropdowns when clicking outside
            document.addEventListener('click', (e) => {
                wrappers.forEach(wrapper => {
                    if (!wrapper.contains(e.target)) {
                        wrapper.classList.remove('open');
                    }
                });
            });

            console.log('{{ $apartments->count() }} apartments loaded with updated shelter amenities.');
        });
    </script>
@endsection