@extends('layouts.dashboard.landpage')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Apartments</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Registered</a></li>
                        <li class="breadcrumb-item active">Apartments</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Search box with button --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="input-group">
                <input type="text" id="search" class="form-control" placeholder="Search buildings or blocks by name, state, landlord, etc...">
                <button id="search-btn" class="btn btn-success" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Location Container --}}
<div class="row" id="locations-container">

    @foreach($locations as $location)

        <div class="col-md-6 col-lg-4 mb-4 searchable-item"
     data-search="{{ strtolower($location['branch_name'].' '.$location['location_name']) }}">

            <div class="card shadow-sm h-100">

                <div class="card-header bg-success">
    <div class="d-flex justify-content-between align-items-center">
        
        <div class="d-flex align-items-center ">
            <i class="ri-building-line text-white me-2 fs-5"></i>
            <span class="text-white fw-semibold ">
                {{ ucwords($location['branch_name']) }}
            </span>
        </div>

        <div class="d-flex align-items-center">
            <i class="ri-map-pin-line text-white me-2 fs-5"></i>
            <span class="text-white fw-semibold">
                {{ ucwords($location['location_name']) }}
            </span>
        </div>

    </div>
</div>

                <div class="card-body">

                    @if(count($location['shelters']) > 0)

                        @foreach($location['shelters'] as $shelter)

                            <a href="{{ route('property.apartments', [
                                'location_id' => $location['location_id'],
                                'shelter_id'  => $shelter['shelter_id']
                            ]) }}"
                               class="text-decoration-none">

                               <div class="border rounded p-3 mb-3 shelter-card shadow-sm"
     data-search="{{ strtolower($shelter['shelter_name']) }}">

    <div class="d-flex justify-content-between align-items-center">

        <div class="d-flex align-items-center">

            <i class="ri-home-4-line text-success fs-4 me-2"></i>

            <div>
                <div class="fw-bold">
                    {{ ucwords($shelter['shelter_name']) }}
                </div>

                <small class="text-muted">
                    Click to view apartments
                </small>
            </div>

        </div>

        <div class="d-flex align-items-center">

            <span class="badge bg-primary me-2">
                {{ number_format($shelter['total_apartments']) }}
                Units
            </span>

            <i class="ri-arrow-right-circle-line text-success fs-4"></i>

        </div>

    </div>

</div>

                            </a>

                        @endforeach

                    @else

                        <div class="alert alert-warning mb-0">
                            No apartments found.
                        </div>

                    @endif

                </div>

            </div>

        </div>

    @endforeach

</div>
  
@endsection

@section('scripts')
   <script>
$(document).ready(function () {

    function filterLocations() {

        let search = $('#search').val().toLowerCase();

        $('.searchable-item').each(function () {

            let locationText = $(this).data('search');

            let shelterText = '';

            $(this).find('.shelter-card').each(function () {
                shelterText += ' ' + $(this).data('search');
            });

            let combinedText = locationText + shelterText;

            if (combinedText.includes(search)) {
                $(this).show();
            } else {
                $(this).hide();
            }

        });
    }

    $('#search-btn').on('click', filterLocations);

    $('#search').on('keyup', filterLocations);

});
</script>

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
            transition: opacity 0.5s ease;
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
@endsection
