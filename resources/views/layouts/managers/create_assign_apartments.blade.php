@extends('layouts.dashboard.landpage')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">

            @if (session('success'))
                <div class="alert alert-success mb-3">
                    {{ session('success') }}
                </div>
            @endif


            {{-- PAGE HEADER --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between px-3">
                        <h4 class="mb-sm-0">Assign Apartments</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('managers.index') }}">Property Managers</a>
                                </li>
                                <li class="breadcrumb-item active">Assign Apartments</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body px-4">

                <h5 class="card-title mb-2">Assign Apartments to <strong>{{ $manager->name }}</strong> in <strong>{{ ucfirst($location_name) }}</strong></h5>
                <p class="card-title-desc mb-4">
                    Select apartments to assign to this property manager.
                </p>
                

                <form method="POST" action="{{ route('managers.assign-apartments') }}">
                    @csrf

                    {{-- SELECT ALL --}}
                    <div class="bg-light p-3 rounded mb-4">
                        <div class="form-check form-switch">
                            <input type="checkbox" id="selectAllApartments" class="form-check-input">
                            <label class="form-check-label">Select All Apartments</label>
                            <input type ='hidden' name ='manager_id' value ="{{$manager->id}}">
                        </div>
                    </div>
                    <div class="row mb-3">
    <div class="col-md-3">
        <input type="text" id="searchRef" class="form-control border-dark" placeholder="Search Property Ref">
    </div>

    <div class="col-md-3">
        <input type="text" id="searchManager" class="form-control border-dark" placeholder="Search Manager Name">
    </div>

    <div class="col-md-3">
        <input type="text" id="searchLandlord" class="form-control border-dark" placeholder="Search Landlord Email">
    </div>

    <div class="col-md-3">
        <input type="text" id="searchAddress" class="form-control border-dark" placeholder="Search Address">
    </div>
</div>

                    {{-- APARTMENTS LIST --}}
                    <div class="row">

                       @foreach($apartments as $apartment)
    <div class="col-md-4 mb-3 apartment-card"
         data-ref="{{ strtolower($apartment->apartment_property_ref) }}"
         data-manager="{{ strtolower($apartment->manager_name ?? '') }}"
         data-landlord="{{ strtolower($apartment->landlord_email ?? '') }}"
         data-address="{{ strtolower($apartment->apartment_address ?? '') }}">

        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                
<strong class="d-block mb-2">Features</strong>

        
            

                <div class="form-check form-switch mb-2">
                    <input type="checkbox"
                           name="apartments[]"
                           value="{{ $apartment->apartment_id }}"
                           class="form-check-input apartment-checkbox border-dark">
                    <label class="form-check-label">Select</label>
                </div>

                <h6 class="text-primary mb-2">
                    {{ $apartment->apartment_property_ref }}
                </h6>


                <p><strong>Branch:</strong> {{ ucfirst($apartment->branch_name) }}</p>
                <p><strong>Location:</strong> {{ ucfirst($apartment->location_name) }}</p>
                <p><strong>Shelter:</strong> {{ ucfirst($apartment->shelter_name) }}</p>
                    <div class="d-flex flex-wrap gap-2">

            @foreach($apartment->amenities as $amenity)
                <span class="badge bg-primary-subtle text-dark border px-3 py-2">
                    {{ ucfirst($amenity['name']) }}
                    <span class="badge bg-primary ms-1">
                        {{ $amenity['number'] }}
                    </span>
                </span>
            @endforeach
</div>
                <hr>

                <p><strong>Address:</strong> {{ $apartment->apartment_address }}</p>
                <p><strong>Landlord:</strong> {{ $apartment->landlord_fname }} {{ $apartment->landlord_lname }}</p>
                <p><strong>Manager:</strong> {{ $apartment->manager_name ?? 'Unassigned' }}</p>


            </div>
        </div>
    </div>
@endforeach
                        
                    </div>
                   <div class="d-flex justify-content-between align-items-center mt-4">
    <button type="button" class="btn btn-secondary btn-sm" id="prevPage">Prev</button>
    
    <div id="pageNumbers" class="d-flex gap-1"></div>
    
    <button type="button" class="btn btn-secondary btn-sm" id="nextPage">Next</button>
</div>
<div class="text-center mt-2">
    <small id="pageInfo" class="text-muted"></small>
</div>

                    {{-- ACTION BUTTONS --}}
                    <div class="mt-4 d-flex justify-content-end gap-2">
    <button type="submit" class="btn btn-success">
        Assign Selected Apartments
    </button>

    <a href="{{ route('managers.index') }}" class="btn btn-secondary">
        Cancel
    </a>
</div>

                </form>

            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Elements
    const cards = Array.from(document.querySelectorAll('.apartment-card'));
    const selectAll = document.getElementById('selectAllApartments');
    
    const searchRef = document.getElementById('searchRef');
    const searchManager = document.getElementById('searchManager');
    const searchLandlord = document.getElementById('searchLandlord');
    const searchAddress = document.getElementById('searchAddress');

    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    const pageNumbersContainer = document.getElementById('pageNumbers');
    const pageInfo = document.getElementById('pageInfo');

    let currentPage = 1;
    const perPage = 6;

    // 1. Get filtered cards based on inputs
    function getFiltered() {
        const ref = searchRef.value.toLowerCase().trim();
        const manager = searchManager.value.toLowerCase().trim();
        const landlord = searchLandlord.value.toLowerCase().trim();
        const address = searchAddress.value.toLowerCase().trim();

        return cards.filter(card => {
            return (
                card.dataset.ref.includes(ref) &&
                card.dataset.manager.includes(manager) &&
                card.dataset.landlord.includes(landlord) &&
                card.dataset.address.includes(address)
            );
        });
    }

    // 2. Render Main Function
    function render() {
        const filtered = getFiltered();
        const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));

        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        // Hide all cards
        cards.forEach(c => c.style.setProperty('display', 'none', 'important'));

        // Show only active chunk
        const start = (currentPage - 1) * perPage;
        const end = start + perPage;
        const visibleCards = filtered.slice(start, end);

        visibleCards.forEach(c => {
            c.style.display = ''; // Safely restores standard col-md-4 grid layout
        });

        // Update Text & Button states
        if(pageInfo) pageInfo.innerText = `Page ${currentPage} of ${totalPages}`;
        prevBtn.disabled = (currentPage === 1);
        nextBtn.disabled = (currentPage === totalPages);

        // Render the numbers
        renderPageNumbers(totalPages);
    }

    // 3. Dynamic Numbered Buttons Generator
    function renderPageNumbers(totalPages) {
        pageNumbersContainer.innerHTML = ''; // Wipe out previous numbers

        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.innerText = i;
            
            // Apply standard Bootstrap classes based on whether it's active
            if (i === currentPage) {
                btn.className = 'btn btn-primary btn-sm px-3';
            } else {
                btn.className = 'btn btn-outline-secondary btn-sm px-3';
            }

            // Click event to hop directly to that page
            btn.addEventListener('click', () => {
                currentPage = i;
                render();
            });

            pageNumbersContainer.appendChild(btn);
        }
    }

    // 4. Select All Logic (Captures ALL matching results across ALL pages)
    selectAll.addEventListener('change', function () {
        const filtered = getFiltered();
        filtered.forEach(card => {
            const cb = card.querySelector('.apartment-checkbox');
            if (cb) cb.checked = this.checked;
        });
    });

    // Inputs Event Listeners
    const inputs = [searchRef, searchManager, searchLandlord, searchAddress];
    inputs.forEach(input => {
        input.addEventListener('input', () => { 
            currentPage = 1; // Reset to page 1 during a new search
            render(); 
        });
    });

    // Prev/Next Navigation
    prevBtn.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            render();
        }
    });

    nextBtn.addEventListener('click', () => {
        const filtered = getFiltered();
        const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
        if (currentPage < totalPages) {
            currentPage++;
            render();
        }
    });

    // Initial Trigger
    render();
});
</script>
@endsection