@extends('layouts.dashboard.landpage')

@section('content')

<div class="container-fluid">

    {{-- PAGE HEADER --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Assigned Apartments</h4>

                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('managers.index') }}">Property Managers</a>
                    </li>
                    <li class="breadcrumb-item active">Assigned Apartments</li>
                </ol>
            </div>
        </div>
    </div>

    {{-- ================= ASSIGN FORM ================= --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">

            <h5 class="mb-3">Assign Apartments</h5>

            <form method="POST" action="{{ route('managers.load-assign-apartments') }}">
                @csrf

                <input type="hidden" name="manager_id" value="{{ $manager_id }}">

                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label">Branch</label>
                        <select name="branch_id" class="form-select" required>
                            <option value="">Select Branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Location</label>
                        <select name="location_id" class="form-select" required>
                            <option value="">Select Location</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Shelter</label>
                        <select name="shelter_id" class="form-select" required>
                            <option value="">Select Shelter</option>
                            @foreach($shelters as $shelter)
                                <option value="{{ $shelter->id }}">{{ ucfirst($shelter->name) }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="mt-3">
                    <button class="btn btn-success">
                        Assign Apartments
                    </button>
                </div>

            </form>

        </div>
    </div>

    {{-- ================= SEARCH FILTER ================= --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-body">

            <div class="row g-2">

                <div class="col-md-3">
                    <input type="text" id="searchRef" class="form-control" placeholder="Search Ref">
                </div>

                <div class="col-md-3">
                    <input type="text" id="searchAddress" class="form-control" placeholder="Search Address">
                </div>

                <div class="col-md-3">
                    <input type="text" id="searchManager" class="form-control" placeholder="Search Manager">
                </div>

                <div class="col-md-3">
                    <input type="text" id="searchShelter" class="form-control" placeholder="Search Shelter">
                </div>

            </div>

        </div>
    </div>

    {{-- ================= EMPTY STATE ================= --}}
    @if($apartments->isEmpty())
        <div class="alert alert-info">
            No apartments assigned to this manager yet.
        </div>
    @endif

    {{-- ================= APARTMENTS ================= --}}
    <div class="row" id="apartmentContainer">

        @foreach($apartments as $locationName => $shelters)

            @foreach($shelters as $shelterName => $items)

                @foreach($items as $apartment)

                    <div class="col-md-4 mb-4 apartment-card"
                        data-ref="{{ strtolower($apartment->apartment_property_ref ?? '') }}"
                        data-address="{{ strtolower($apartment->address ?? '') }}"
                        data-manager="{{ strtolower($apartment->manager_name ?? '') }}"
                        data-shelter="{{ strtolower($apartment->shelter_name ?? '') }}">

                        <div class="card shadow-sm border-0 h-100">

                            <div class="card-body">

                                <h6 class="text-primary mb-2">
                                    {{ $apartment->apartment_property_ref }}
                                </h6>

                                <p class="mb-1"><strong>Branch:</strong> {{ ucfirst($apartment->branch_name) }}</p>
                                <p class="mb-1"><strong>Location:</strong> {{ ucfirst($apartment->location_name) }}</p>
                                <p class="mb-1"><strong>Shelter:</strong> {{ ucfirst($apartment->shelter_name) }}</p>

                                <hr>

                                <p class="mb-1"><strong>Address:</strong> {{ $apartment->address }}</p>
                            
                                <p class="mb-1"><strong>Manager:</strong> {{ $apartment->manager_name ?? 'Unassigned' }}</p>

                                <small class="text-muted">
                                    Created: {{ $apartment->created_at ?? 'N/A' }}
                                </small>

                                {{-- ================= AMENITIES ================= --}}
                                @if($apartment->amenities->count())
                                    <hr>

                                    <div class="d-flex flex-wrap gap-1 align-items-center">
                                        <strong class="me-1">Amenities:</strong>

                                        @foreach($apartment->amenities as $amenity)
                                            <span class="badge bg-primary">
                                                {{ ucfirst($amenity['name']) }} {{ $amenity['number'] }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                            </div>
                        </div>

                    </div>

                @endforeach

            @endforeach

        @endforeach

    </div>

</div>

@endsection


@section('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const cards = document.querySelectorAll('.apartment-card');

    const searchRef = document.getElementById('searchRef');
    const searchAddress = document.getElementById('searchAddress');
    const searchManager = document.getElementById('searchManager');
    const searchShelter = document.getElementById('searchShelter');

    function filterCards() {

        const ref = searchRef.value.toLowerCase();
        const address = searchAddress.value.toLowerCase();
        const manager = searchManager.value.toLowerCase();
        const shelter = searchShelter.value.toLowerCase();

        cards.forEach(card => {

            const match =
                card.dataset.ref.includes(ref) &&
                card.dataset.address.includes(address) &&
                card.dataset.manager.includes(manager) &&
                card.dataset.shelter.includes(shelter);

            card.style.display = match ? '' : 'none';
        });
    }

    [searchRef, searchAddress, searchManager, searchShelter].forEach(input => {
        input.addEventListener('input', filterCards);
    });
    const branchSelect = document.querySelector('select[name="branch_id"]');
    const locationSelect = document.querySelector('select[name="location_id"]');

    const locations = @json($locations ?? []);

    if (!branchSelect || !locationSelect) return;

    branchSelect.addEventListener('change', function () {

        const branchId = this.value;

        // reset
        locationSelect.innerHTML = '<option value="">Select Location</option>';

        if (!branchId) return;

        const filteredLocations = locations.filter(loc => loc.branch_id == branchId);

        filteredLocations.forEach(loc => {
            const option = document.createElement('option');
            option.value = loc.id;
            option.textContent = loc.name;
            locationSelect.appendChild(option);
        });

    });

});
</script>

@endsection