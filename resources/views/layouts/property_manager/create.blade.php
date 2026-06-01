@extends('layouts.dashboard.landpage')

@section('content')
    <style>
        .twitter-bs-wizard-tab-content { padding: 20px; }
        .nav-link { cursor: pointer; }
        .nav-link.disabled { pointer-events: none; opacity: 0.65; }
        .shelter-item { display: flex; align-items: center; margin-bottom: 10px; }
        .shelter-item label { flex: 1; margin-bottom: 0; }
        .shelter-item .input-group { width: 150px; margin-left: 15px; }
        .custom-select-wrapper { position: relative; width: 100%; }
        .custom-select-search { width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px; }
        .custom-select-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            border: 1px solid #ced4da;
            background: white;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
        }
        .custom-select-option {
            padding: 8px;
            cursor: pointer;
        }
        .custom-select-option:hover { background: #f8f9fa; }
        .custom-select-wrapper.open .custom-select-dropdown { display: block; }
        .nav-link.active {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
            color: white !important;
        }
        #custom-preloader {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(40, 167, 69, 0.8);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.5s ease;
        }
        #custom-preloader.active {
            opacity: 1;
            pointer-events: auto;
        }
        #custom-preloader .spinner {
            font-size: 36px;
            color: white;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .twitter-bs-wizard-pager-link .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
        }
        .twitter-bs-wizard-pager-link .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .invalid-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }
        .is-invalid ~ .invalid-feedback {
            display: block;
        }
        .custom-select-search.is-invalid {
            border-color: #dc3545;
        }
    </style>

    <!-- Custom Green Preloader -->
    <div id="custom-preloader">
        <div class="spinner">
            <i class="ri-loader-line"></i>
        </div>
    </div>

    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Property Profile Manager</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Property Profile Manager</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- End Page Title -->

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Create New Property</h4>

                    @if (session('success'))
                        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div id="progrss-wizard" class="twitter-bs-wizard">
                        <ul class="nav nav-pills nav-justified mb-4" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="block-details-tab" data-bs-toggle="tab" href="#block-details" role="tab" aria-controls="block-details" aria-selected="true">
                                    Step 1: Property Details
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="shelter-details-tab" data-bs-toggle="tab" href="#shelter-details" role="tab" aria-controls="shelter-details" aria-selected="false">
                                    Step 2: Shelter Details
                                </a>
                            </li>
                        </ul>

                        <div id="bar" class="progress mt-4">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: 50%;" id="progressBar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>

                        <form id="blockForm" method="POST" action="{{ route('property.store') }}">
                            @csrf

                            <div class="tab-content twitter-bs-wizard-tab-content">
                               <div class="tab-pane fade show active"
     id="block-details"
     role="tabpanel"
     aria-labelledby="block-details-tab">

    <div class="row">
  {{-- Branch --}}
       <div class="col-md-6">
    <div class="mb-3">
        <label class="form-label">Branch</label>

        <div class="custom-select-wrapper" id="branch-wrapper">

            <input type="text"
                   class="custom-select-search @error('branch_id') is-invalid @enderror"
                   placeholder="Search Branch..."
                   autocomplete="off">

            <input type="hidden"
                   name="branch_id"
                   id="branch_id"
                   value="{{ old('branch_id') }}">

            <div class="custom-select-dropdown">

                @foreach($branches as $branch)
                    <div class="custom-select-option"
                         data-value="{{ $branch->id }}">
                        {{ $branch->name }}
                    </div>
                @endforeach

            </div>

        </div>

        @error('branch_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>
       
        <div class="col-md-6">
    <div class="mb-3">
        <label class="form-label">Location</label>

        <div class="custom-select-wrapper" id="location-wrapper">

            <input type="text"
                   class="custom-select-search @error('location_id') is-invalid @enderror"
                   placeholder="Search Location..."
                   autocomplete="off">

            <input type="hidden"
                   name="location_id"
                   id="location_id"
                   value="{{ old('location_id') }}">

            <div class="custom-select-dropdown" id="location-dropdown">

                @foreach($locations as $location)
                    <div class="custom-select-option"
                         data-value="{{ $location->id }}"
                         data-branch="{{ $location->branch_id }}">
                        {{ $location->name }}
                    </div>
                @endforeach

            </div>

        </div>

        @error('location_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>
        </div>


    <div class="row">

      

        {{-- Address --}}
        <div class="col-md-6">
            <div class="mb-3">

                <label class="form-label" for="address">
                    Address
                </label>

                <input type="text"
                       class="form-control @error('address') is-invalid @enderror"
                       id="address"
                       name="address"
                       value="{{ old('address') }}"
                       required>

                @error('address')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>
        </div>
      <div class="col-md-6">
    <div class="mb-3">

        <label class="form-label">
            Landlord
        </label>

        <div class="custom-select-wrapper">

            <input type="text"
                   class="custom-select-search @error('landlord_id') is-invalid @enderror"
                   placeholder="Search Landlord..."
                   autocomplete="off">

            <input type="hidden"
                   name="landlord_id"
                   id="landlord_id"
                   value="{{ old('landlord_id') }}">

            <div class="custom-select-dropdown">

                @foreach($landlords as $landlord)
                    <div class="custom-select-option"
                         data-value="{{ $landlord->id }}">
                        {{ $landlord->fName }}
                        {{ $landlord->lName }}
                        ({{ $landlord->email }})
                    </div>
                @endforeach

            </div>

        </div>

        @error('landlord_id')
            <div class="invalid-feedback d-block">
                {{ $message }}
            </div>
        @enderror

    </div>
</div>

    </div>

    

</div>

                                <div class="tab-pane fade" id="shelter-details" role="tabpanel" aria-labelledby="shelter-details-tab">
                                    <h5 class="mb-3">Shelter Types and Quantities</h5>
                                    <div id="shelter-container">
                                        <p class="text-muted text-center">Kindly enter the number of each type of accommodation in this block</p>
                                        @foreach ($shelters as $shelter)
                                            <div class="shelter-item">
                                                <label class="form-label">{{ ucwords($shelter->name) }}</label>
                                                <div class="input-group">
                                                    <input type="hidden" name="shelter_name[{{ $shelter->id }}]" value="{{ $shelter->name }}">
                                                    <input type="number" class="form-control @error('shelter_qty.' . $shelter->id) is-invalid @enderror"
                                                           name="shelter_qty[{{ $shelter->id }}]" min="0"
                                                           value="{{ old('shelter_qty.' . $shelter->id, 0) }}" required>
                                                    <span class="input-group-text">Qty</span>
                                                    @error('shelter_qty.' . $shelter->id)
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4">

    <button type="button"
            class="btn btn-secondary previous-btn">
        Previous
    </button>

    <div>
        <button type="button"
                class="btn btn-success next-btn">
            Next
        </button>

        <button type="submit"
                class="btn btn-success submit-btn d-none">
            Save
        </button>
    </div>

</div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {



        // Branch -> Location filtering
        const branchInput = document.getElementById('branch_id');
const locationDropdown = document.getElementById('location-dropdown');

if (branchInput && locationDropdown) {

    const allLocations = Array.from(
        locationDropdown.querySelectorAll('.custom-select-option')
    );

    function filterLocationsByBranch() {

        const branchId = branchInput.value;

        allLocations.forEach(location => {

            if (!branchId) {

                location.style.display = 'none';
                return;

            }

            location.style.display =
                location.dataset.branch === branchId
                ? 'block'
                : 'none';

        });

    }

    branchInput.addEventListener(
        'change',
        filterLocationsByBranch
    );

    filterLocationsByBranch();
}
            // Custom select functionality
         

            // Wizard stepper logic
            const navLinks = document.querySelectorAll('.nav-link');
            const progressBar = document.getElementById('progressBar');
            const tabs = document.querySelectorAll('.tab-pane');
            const form = document.getElementById('blockForm');
           const prevBtn = document.querySelector('.previous-btn');
           const nextBtn = document.querySelector('.next-btn');
           const submitBtn = document.querySelector('.submit-btn');
// add null checks
if (!prevBtn || !nextBtn || !submitBtn) {
    console.error('Wizard buttons not found.');
    return;
}


            // Update UI based on current tab
            const updateWizard = (index) => {
                tabs.forEach(tab => tab.classList.remove('show', 'active'));
                navLinks.forEach(link => link.classList.remove('active'));

                navLinks[index].classList.add('active');
                const targetTab = document.querySelector(navLinks[index].getAttribute('href'));
                targetTab.classList.add('show', 'active');

                const percent = (index / (navLinks.length - 1)) * 100;
                progressBar.style.width = `${percent}%`;
                progressBar.setAttribute('aria-valuenow', percent);

                // Toggle button visibility
                prevBtn.disabled = index === 0;
                nextBtn.classList.toggle('d-none', index === navLinks.length - 1);
                submitBtn.classList.toggle('d-none', index !== navLinks.length - 1);
            };

            // Validate current tab
            const validateTab = (tab) => {
                const inputs = tab.querySelectorAll('input[required], select[required]');
                let isValid = true;
                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        input.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });
                return isValid;
            };

            // Handle navigation link clicks
            navLinks.forEach((link, index) => {
                link.addEventListener('click', e => {
                    e.preventDefault();
                    const currentTab = document.querySelector('.tab-pane.show.active');
                    if (validateTab(currentTab)) {
                        updateWizard(index);
                    }
                });
            });

            // Handle next button
            nextBtn.addEventListener('click', () => {
                const currentTab = document.querySelector('.tab-pane.show.active');
                const currentIndex = Array.from(tabs).indexOf(currentTab);
                if (validateTab(currentTab) && currentIndex < navLinks.length - 1) {
                    updateWizard(currentIndex + 1);
                }
            });

            // Handle previous button
            prevBtn.addEventListener('click', () => {
                const currentTab = document.querySelector('.tab-pane.show.active');
                const currentIndex = Array.from(tabs).indexOf(currentTab);
                if (currentIndex > 0) {
                    updateWizard(currentIndex - 1);
                }
            });

            // Handle form submission
            form.addEventListener('submit', e => {
                const currentTab = document.querySelector('.tab-pane.show.active');
                if (!validateTab(currentTab)) {
                    e.preventDefault();
                } else {
                    document.getElementById('custom-preloader').classList.add('active');
                }
            });

            document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {

    const searchInput =
        wrapper.querySelector('.custom-select-search');

    const hiddenInput =
        wrapper.querySelector('input[type="hidden"]');

    const dropdown =
        wrapper.querySelector('.custom-select-dropdown');

    searchInput.addEventListener('focus', () => {
        wrapper.classList.add('open');
    });

    searchInput.addEventListener('input', () => {

        const filter =
            searchInput.value.toLowerCase();

        dropdown.querySelectorAll('.custom-select-option')
            .forEach(option => {

                option.style.display =
                    option.textContent
                    .toLowerCase()
                    .includes(filter)
                    ? ''
                    : 'none';

            });

        wrapper.classList.add('open');
    });

    dropdown.querySelectorAll('.custom-select-option')
        .forEach(option => {

           option.addEventListener('click', () => {

    searchInput.value =
        option.textContent.trim();

    hiddenInput.value =
        option.dataset.value;

    hiddenInput.dispatchEvent(
        new Event('change')
    );

    wrapper.classList.remove('open');
});
        });

});
        });
    </script>
@endsection