<style>

.apartment-card .card{
    overflow:hidden;
}

.text-break{
    word-break: break-word;
    overflow-wrap: anywhere;
}

.accordion-body{
    overflow-x:hidden;
}

.accordion-button{
    font-size:.9rem;
}

.card-header{
    word-break:break-word;
}

</style>
@extends('layouts.dashboard.landpage')

@section('content')

<div class="card shadow-sm mb-4">
    <div class="card-body">

        <div class="row align-items-center">

            <div class="col-md-6">
                <h4 class="mb-1">
                    {{ ucwords($location->name) }}
                </h4>

                <small class="text-muted">
                    Total Apartments: {{ $apartments->count() }}
                </small>
            </div>

            <div class="col-md-6">
                <input
                    type="text"
                    id="apartmentSearch"
                    class="form-control"
                    placeholder="Search by address, tenant name or landlord name..."
                >
            </div>

        </div>

    </div>
</div>

<div class="row" id="apartmentsContainer">

@forelse($apartments as $apartment)

<div
    class="col-xl-4 col-lg-4 col-md-6 mb-4 apartment-card"
    data-address="{{ strtolower($apartment->address ?? '') }}"
    data-tenant="{{ strtolower($apartment->tenant_full_name ?? '') }}"
    data-landlord="{{ strtolower(trim(($apartment->estate_owner_fName ?? '').' '.($apartment->estate_owner_lName ?? ''))) }}"
>

    <div class="card shadow-sm border-0 h-100">

        <div class="card-body">

            <div class="border-bottom pb-2 mb-3">

                <h6 class="fw-bold mb-1 text-primary">
                    {{ $apartment->address }}
                </h6>

                <span class="badge bg-dark">
                    Unit {{ $apartment->unit_number }}
                </span>

            </div>

            <div class="mb-2">

                @if($apartment->tenant_id)
                    <span class="badge bg-danger">
                        Occupied
                    </span>
                @else
                    <span class="badge bg-success">
                        Vacant
                    </span>
                @endif

                @if($apartment->estate_owner_id)
                    <span class="badge bg-primary">
                        Landlord Assigned
                    </span>
                @else
                    <span class="badge bg-secondary">
                        No Landlord
                    </span>
                @endif

            </div>

            <div class="mb-3">

                <strong class="d-block mb-2">
                    Amenities
                </strong>

                @forelse($apartment->amenities as $amenity)

                    <span class="badge bg-info text-white mb-1">

                        {{ ucfirst($amenity->amenities->name) }}

                        @if($amenity->amenity_number > 0)
                            ({{ $amenity->amenity_number }})
                        @endif

                    </span>

                @empty

                    <span class="badge bg-warning text-white mb-1">
                        No Amenities
                    </span>

                @endforelse

            </div>

            <div class="accordion" id="accordion{{ $apartment->id }}">

                <div class="accordion-item">

                    <h2 class="accordion-header">

                        <button
                            class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#details{{ $apartment->id }}"
                        >
                            View Details
                        </button>

                    </h2>

                    <div
                        id="details{{ $apartment->id }}"
                        class="accordion-collapse collapse"
                    >

                       <div class="accordion-body">

    {{-- Apartment Details --}}
    <div class="card border-primary mb-3">
        <div class="card-header bg-primary text-white">
            Apartment Details
        </div>

        <div class="card-body">

            <div class="row g-2">

                <div class="col-5 fw-bold">
                    Property Ref
                </div>

                <div class="col-7 text-break">
                    {{ $apartment->property_ref }}
                </div>

                <div class="col-5 fw-bold">
                    Ownership
                </div>

                <div class="col-7 text-break">
                    {{ $apartment->ownership }}
                </div>

                <div class="col-5 fw-bold">
                    Fee
                </div>

                <div class="col-7 text-break">
                    ₦{{ number_format($apartment->fee,2) }}
                </div>

                <div class="col-5 fw-bold">
                    Payment
                </div>

                <div class="col-7 text-break">
                    {{ optional($pay_time->firstWhere('id',$apartment->pay_frequency_id))->payment_frequency }}
                </div>

            </div>

        </div>
    </div>

    {{-- Landlord --}}
    <div class="card border-success mb-3">

        <div class="card-header bg-success text-white">
            Landlord Details
        </div>

        <div class="card-body">

            @if($apartment->estate_owner_id)

                <div class="row g-2">

                    <div class="col-4 fw-bold">
                        Name
                    </div>

                    <div class="col-8 text-break">
                        {{ $apartment->estate_owner_fName }}
                        {{ $apartment->estate_owner_lName }}
                    </div>

                    <div class="col-4 fw-bold">
                        Email
                    </div>

                    <div class="col-8 text-break">
                        {{ $apartment->estate_owner_email }}
                    </div>

                    <div class="col-4 fw-bold">
                        Phone
                    </div>

                    <div class="col-8 text-break">
                        {{ $apartment->estate_owner_phones }}
                    </div>

                </div>

            @else

                <div class="alert alert-warning mb-0">
                    No landlord assigned.
                </div>

            @endif

        </div>

    </div>

    {{-- Tenant --}}
    <div class="card border-warning mb-3">

        <div class="card-header bg-warning text-white">
            Tenant Details
        </div>

        <div class="card-body">

            @if($apartment->tenant_id)

                <div class="row g-2">

                    <div class="col-4 fw-bold">
                        Name
                    </div>

                    <div class="col-8 text-break">
                        {{ $apartment->tenant_full_name }}
                    </div>

                    <div class="col-4 fw-bold">
                        Gender
                    </div>

                    <div class="col-8 text-break">
                        {{ $apartment->tenant_gender }}
                    </div>

                    <div class="col-4 fw-bold">
                        Phone
                    </div>

                    <div class="col-8 text-break">
                        {{ $apartment->tenant_mobile_number }}
                    </div>

                    <div class="col-4 fw-bold">
                        Email
                    </div>

                    <div class="col-8 text-break">
                        {{ $apartment->tenant_email }}
                    </div>

                    <div class="col-4 fw-bold">
                        Start
                    </div>

                    <div class="col-8 text-break">
                        {{ $apartment->booking_start_date }}
                    </div>

                    <div class="col-4 fw-bold">
                        End
                    </div>

                    <div class="col-8 text-break">
                        {{ $apartment->booking_end_date }}
                    </div>

                </div>

            @else

                <div class="alert alert-info mb-0">
                    Apartment currently vacant.
                </div>

            @endif

        </div>

    </div>

    {{-- Amenities --}}
    <div class="card border-info">

        <div class="card-header bg-info text-white">
            Amenities Breakdown
        </div>

        <div class="card-body">

            <div class="row">

                @forelse($apartment->amenities as $amenity)

                    <div class="col-12 mb-2">

                        <div class="d-flex justify-content-between border rounded p-2">

                            <span class="text-break">
                                {{ $amenity->amenities->name }}
                            </span>

                            <span class="badge bg-success">
                                {{ $amenity->amenity_number }}
                            </span>

                        </div>

                    </div>

                @empty

                    <div class="col-12">

                        <div class="alert alert-secondary mb-0">
                            No amenities configured.
                        </div>

                    </div>

                @endforelse

            </div>

        </div>

    </div>

</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@empty

<div class="col-12">
    <div class="alert alert-warning">
        No apartments found.
    </div>
</div>

@endforelse

</div>

<div class="d-flex justify-content-center mt-4">
    <nav>
        <ul class="pagination" id="pagination"></ul>
    </nav>
</div>

@endsection

@section('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const cardsPerPage = 12;

    let currentPage = 1;

    const cards = Array.from(
        document.querySelectorAll('.apartment-card')
    );

    const pagination = document.getElementById('pagination');

    const searchInput = document.getElementById('apartmentSearch');

    function displayCards(page)
    {
        const visibleCards = cards.filter(
            card => card.style.display !== 'none-filtered'
        );

        cards.forEach(card => {
            if(card.style.display !== 'none-filtered'){
                card.style.display = 'none';
            }
        });

        const start = (page - 1) * cardsPerPage;
        const end = start + cardsPerPage;

        visibleCards.slice(start, end).forEach(card => {
            card.style.display = '';
        });

        createPagination(visibleCards.length);
    }

    function createPagination(totalItems)
    {
        pagination.innerHTML = '';

        const totalPages =
            Math.ceil(totalItems / cardsPerPage);

        if(totalPages <= 1){
            return;
        }

        for(let i = 1; i <= totalPages; i++)
        {
            const li = document.createElement('li');

            li.className =
                'page-item ' +
                (i === currentPage ? 'active' : '');

            li.innerHTML =
                `<a class="page-link" href="#">${i}</a>`;

            li.addEventListener('click', function(e){

                e.preventDefault();

                currentPage = i;

                displayCards(currentPage);

            });

            pagination.appendChild(li);
        }
    }

    searchInput.addEventListener('keyup', function(){

        const search =
            this.value.toLowerCase();

        cards.forEach(function(card){

            const address =
                card.dataset.address || '';

            const tenant =
                card.dataset.tenant || '';

            const landlord =
                card.dataset.landlord || '';

            if(
                address.includes(search) ||
                tenant.includes(search) ||
                landlord.includes(search)
            ){
                card.style.display = '';
                card.style.removeProperty('none-filtered');
            }
            else{
                card.style.display = 'none-filtered';
            }

        });

        currentPage = 1;

        displayCards(currentPage);

    });

    displayCards(1);

});

</script>

@endsection