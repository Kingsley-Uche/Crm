
@extends('layouts.dashboard.landpage')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>

@endif

<div class="row px-3 py-3"><!-- Added horizontal and vertical padding -->
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Create Occupant</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('occupant.index') }}">Occupants Manager</a></li>
                    <li class="breadcrumb-item active">Create Occupant</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
            <div class="card-body">
                <h3 class="card-title text-center">Occupant Registration Form</h3>
                <p class="card-title-desc text-center">
                    Please fill in the required details below. All fields marked with 
                    <span class="text-danger">*</span> are mandatory.
                </p>

                <form method="POST" enctype="multipart/form-data" id="occupantRegistrationWizard" action="{{ route('occupant.store') }}">
                    @csrf
                    
                    <!-- Wizard Navigation -->
                    <ul class="nav nav-pills nav-justified mb-4" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#basic-details">
                                Step 1: Occupant Basic Details
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#identification-details">
                                Step 2: Identification Details
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#contact-details">
                                Step 3: Contact Details
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#guarantor-details">
                                Step 4: Next of Kin Details
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Step 1: Occupant Basic Details -->
                        <div class="tab-pane active" id="basic-details">
                            <h5 class="mt-4 mb-3 text-center">Occupant Basic Details</h5>
                            
                            <!-- First Name -->
                            <div class="row mb-3">
                                <label for="full_name" class="col-sm-3 col-form-label">
                                    First Name <span class="text-danger">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input type="text" name="full_name" id="full_name" 
                                           class="form-control @error('full_name') is-invalid @enderror" 
                                           value="{{ old('full_name') }}" 
                                           placeholder="Enter full name" required>
                                    @error('full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Last Name -->
                            <div class="row mb-3">
                                <label for="last_name" class="col-sm-3 col-form-label">
                                    Last Name <span class="text-danger">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input type="text" name="last_name" id="last_name" 
                                           class="form-control @error('last_name') is-invalid @enderror" 
                                           value="{{ old('last_name') }}" 
                                           placeholder="Enter last name" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Middle Name -->
                            <div class="row mb-3">
                                <label for="middle_name" class="col-sm-3 col-form-label">
                                    Middle Name
                                </label>
                                <div class="col-sm-9">
                                    <input type="text" name="middle_name" id="middle_name" 
                                           class="form-control @error('middle_name') is-invalid @enderror" 
                                           value="{{ old('middle_name') }}" 
                                           placeholder="Enter middle name (optional)">
                                    @error('middle_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Date of Birth -->
                            <div class="row mb-3">
                                <label for="date_of_birth" class="col-sm-3 col-form-label">
                                    Date of Birth <span class="text-danger">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input type="date" name="date_of_birth" id="date_of_birth" 
                                           class="form-control @error('date_of_birth') is-invalid @enderror" 
                                           value="{{ old('date_of_birth') }}" required>
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Gender -->
                            <div class="row mb-3">
                                <label for="gender" class="col-sm-3 col-form-label">
                                    Gender <span class="text-danger">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <select name="gender" id="gender" 
                                            class="form-select @error('gender') is-invalid @enderror" required>
                                        <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select gender</option>
                                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-sm-12 text-center">
                                    <button type="button" class="btn btn-success next-tab">
                                        Next
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Step 2: Identification Details -->
                        <div class="tab-pane" id="identification-details">
                            <h5 class="mt-4 mb-3 text-center">Identification Details</h5>
                            
                            <!-- Nationality -->
                            <div class="row mb-3">
                                <label for="nationality" class="col-sm-3 col-form-label">
                                    Nationality <span class="text-danger">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input type="text" name="nationality" id="nationality" 
                                           class="form-control @error('nationality') is-invalid @enderror" 
                                           value="{{ old('nationality') }}" 
                                           placeholder="Enter nationality" required>
                                    @error('nationality')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- State -->
                            <div class="row mb-3">
                                <label for="state" class="col-sm-3 col-form-label">
                                    State <span class="text-danger">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input type="text" name="state" id="state" 
                                           class="form-control @error('state') is-invalid @enderror" 
                                           value="{{ old('state') }}" 
                                           placeholder="Enter state" required>
                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Address -->
                            <div class="row mb-3">
                                <label for="address" class="col-sm-3 col-form-label">
                                    Address <span class="text-danger">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <textarea name="address" id="address" 
                                              class="form-control @error('address') is-invalid @enderror" 
                                              placeholder="Enter address" required>{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Method of Identification -->
                            <div class="row mb-3">
                                <label for="id_method" class="col-sm-3 col-form-label">
                                    Method of Identification <span class="text-danger">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <select name="id_method" id="id_method" 
                                            class="form-select @error('id_method') is-invalid @enderror" required>
                                        <option value="" disabled {{ old('id_method') ? '' : 'selected' }}>Select method</option>
                                        <option value="passport" {{ old('id_method') === 'passport' ? 'selected' : '' }}>Passport</option>
                                        <option value="driver_licence" {{ old('id_method') === 'driver_licence' ? 'selected' : '' }}>Driver's Licence</option>
                                    
                                    </select>
                                    @error('id_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Identification Image -->
                            <div class="row mb-3">
                                <label for="identification_image" class="col-sm-3 col-form-label">
                                    Identification Image <span class="text-danger">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input type="file" name="identification_image" id="identification_image" 
                                           class="form-control @error('identification_image') is-invalid @enderror" 
                                           accept=".png,.jpeg,.jpg" required>
                                    @error('identification_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Passport Photograph -->
                            <div class="row mb-3">
                                <label for="passport_photograph" class="col-sm-3 col-form-label">
                                    Passport Photograph <span class="text-danger">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input type="file" name="passport_photograph" id="passport_photograph" 
                                           class="form-control @error('passport_photograph') is-invalid @enderror" 
                                           accept=".png,.jpeg,.jpg">
                                    @error('passport_photograph')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-sm-12 text-center">
                                    <button type="button" class="btn btn-secondary prev-tab">
                                        Previous
                                    </button>
                                    <button type="button" class="btn btn-success next-tab">
                                        Next
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Step 3: Contact Details -->
                        <div class="tab-pane" id="contact-details">
                            <h5 class="mt-4 mb-3 text-center">Contact Details</h5>
                            
                            <!-- Mobile Number -->
                            <div class="row mb-3">
                                <label for="mobile_number" class="col-sm-3 col-form-label">
                                    Mobile Number <span class="text-danger">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input type="tel" name="mobile_number" id="mobile_number" 
                                           class="form-control @error('mobile_number') is-invalid @enderror" 
                                           value="{{ old('mobile_number') }}" 
                                           placeholder="Enter mobile number" required>
                                    @error('mobile_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Home Number -->
                            <div class="row mb-3">
                                <label for="home_number" class="col-sm-3 col-form-label">
                                    Home Number <span class="text-danger">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input type="tel" name="home_number" id="home_number" 
                                           class="form-control @error('home_number') is-invalid @enderror" 
                                           value="{{ old('home_number') }}" 
                                           placeholder="Enter home number" required>
                                    @error('home_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Occupant Email -->
                            <div class="row mb-3">
                                <label for="occupant_email" class="col-sm-3 col-form-label">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input type="email" name="occupant_email" id="occupant_email" 
                                           class="form-control @error('occupant_email') is-invalid @enderror" 
                                           value="{{ old('occupant_email') }}" 
                                           placeholder="Enter email" required>
                                    @error('occupant_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Emergency Contact Number -->
                            <div class="row mb-3">
                                <label for="emergency_contact" class="col-sm-3 col-form-label">
                                    Emergency Contact <span class="text-danger">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input type="tel" name="emergency_contact" id="emergency_contact" 
                                           class="form-control @error('emergency_contact') is-invalid @enderror" 
                                           value="{{ old('emergency_contact') }}" 
                                           placeholder="Enter emergency contact number" required>
                                    @error('emergency_contact')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Emergency Contact Email -->
                            <div class="row mb-3">
                                <label for="emergency_email" class="col-sm-3 col-form-label">
                                    Emergency Email 
                                </label>
                                <div class="col-sm-9">
                                    <input type="email" name="emergency_email" id="emergency_email" 
                                           class="form-control @error('emergency_email') is-invalid @enderror" 
                                           value="{{ old('emergency_email') }}" 
                                           placeholder="Enter emergency email">
                                    @error('emergency_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-sm-12 text-center">
                                    <button type="button" class="btn btn-secondary prev-tab">
                                        Previous
                                    </button>
                                    <button type="button" class="btn btn-success next-tab">
                                        Next
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Step 4: Guarantor Details -->
                        <div class="tab-pane" id="guarantor-details">
                            <h5 class="mt-4 mb-3 text-center">Next of Kin Details</h5>

                            <!-- Full Name -->
                            <div class="row mb-3">
                                <label for="guarantor_full_name" class="col-sm-3 col-form-label">
                                    Full Name <span class="text-danger">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input type="text" name="guarantor_full_name" id="guarantor_full_name" 
                                           class="form-control @error('guarantor_full_name') is-invalid @enderror" 
                                           value="{{ old('guarantor_full_name') }}" 
                                           placeholder="Enter full name" required>
                                    @error('guarantor_full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Address -->
                            <div class="row mb-3">
                                <label for="guarantor_address" class="col-sm-3 col-form-label">
                                    Address 
                                </label>
                                <div class="col-sm-9">
                                    <textarea name="guarantor_address" id="guarantor_address" 
                                              class="form-control @error('guarantor_address') is-invalid @enderror" 
                                              placeholder="Enter address" required>{{ old('guarantor_address') }}</textarea>
                                    @error('guarantor_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                             <div class="row mb-3">
                                <label for="guarantor_email" class="col-sm-3 col-form-label">
                                    Next of Kin Email
                                </label>
                                <div class="col-sm-9">
                                    <input type="email" name="guarantor_email" id="guarantor_email" 
                                           class="form-control @error('guarantor_email') is-invalid @enderror" 
                                           value="{{ old('guarantor_email') }}" 
                                           placeholder="Enter email address of next of kin">
                                    @error('guarantor_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- Phone -->
                            <div class="row mb-3">
                                <label for="guarantor_phone" class="col-sm-3 col-form-label">
                                    Phone <span class="text-danger">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input type="tel" name="guarantor_phone" id="guarantor_phone" 
                                           class="form-control @error('guarantor_phone') is-invalid @enderror" 
                                           value="{{ old('guarantor_phone') }}" 
                                           placeholder="Enter phone number" required>
                                    @error('guarantor_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            
                            <div class="row mt-4">
                                <div class="col-sm-12 text-center">
                                    <button type="button" class="btn btn-secondary prev-tab">
                                        Previous
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        Submit Registration
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<style>
    /* Override Bootstrap's default blue active tab color with green */
    .nav-pills .nav-link.active {
        background-color:#77c44e !important; /* Bootstrap's success green */
        border-color:#77c44e !important;
        color: white !important;
    }

    .nav-pills .nav-link {
        color: #495057; /* Default text color for inactive tabs */
    }

    .nav-pills .nav-link:hover:not(.active) {
        background-color: #e9ecef; /* Light gray hover effect */
    }
</style>
@endsection
@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Function to check if all required fields in a tab are filled
    function areRequiredFieldsFilled(tabPane) {
        const requiredFields = tabPane.querySelectorAll('[required]');
        let allFilled = true;
        
        requiredFields.forEach(field => {
            if (field.type === 'file') {
                if (!field.files.length) {
                    allFilled = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            } else if (field.tagName === 'SELECT') {
                if (!field.value || field.value === '') {
                    allFilled = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            } else {
                if (!field.value.trim()) {
                    allFilled = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            }
        });
        
        return allFilled;
    }

    // NEXT TAB: move to the next tab-pane only if required fields are filled
    document.querySelectorAll('.next-tab').forEach(function(button) {
        button.addEventListener('click', function(e) {
            const currentTab = this.closest('.tab-pane');
            
            // Check if all required fields are filled
            if (!areRequiredFieldsFilled(currentTab)) {
                e.preventDefault();
                alert('Please fill all mandatory fields before proceeding.');
                return;
            }

            let nextTab = currentTab.nextElementSibling;
            while (nextTab && !nextTab.classList.contains('tab-pane')) {
                nextTab = nextTab.nextElementSibling;
            }
            
            if (nextTab) {
                const nextTabId = nextTab.getAttribute('id');
                const navLink = document.querySelector('.nav-link[href="#' + nextTabId + '"]');
                if (navLink) {
                    let tabInstance = bootstrap.Tab.getInstance(navLink);
                    if (!tabInstance) {
                        tabInstance = new bootstrap.Tab(navLink);
                    }
                    tabInstance.show();
                }
            }
        });
    });
    
    // PREVIOUS TAB: move to the previous tab-pane (no validation needed)
    document.querySelectorAll('.prev-tab').forEach(function(button) {
        button.addEventListener('click', function() {
            const currentTab = this.closest('.tab-pane');
            let prevTab = currentTab.previousElementSibling;
            while (prevTab && !prevTab.classList.contains('tab-pane')) {
                prevTab = prevTab.previousElementSibling;
            }
            if (prevTab) {
                const prevTabId = prevTab.getAttribute('id');
                const navLink = document.querySelector('.nav-link[href="#' + prevTabId + '"]');
                if (navLink) {
                    let tabInstance = bootstrap.Tab.getInstance(navLink);
                    if (!tabInstance) {
                        tabInstance = new bootstrap.Tab(navLink);
                    }
                    tabInstance.show();
                }
            }
        });
    });

    // Optional: Real-time validation feedback
    document.querySelectorAll('[required]').forEach(field => {
        field.addEventListener('change', function() {
            if (this.type === 'file') {
                this.files.length ? this.classList.remove('is-invalid') : this.classList.add('is-invalid');
            } else {
                this.value.trim() ? this.classList.remove('is-invalid') : this.classList.add('is-invalid');
            }
        });
    });
});
</script>
@endsection
