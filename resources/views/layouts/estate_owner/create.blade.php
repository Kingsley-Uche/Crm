@extends('layouts.dashboard.landpage')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Create Estate Owner</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <!-- Fixed route name to match controller -->
                    <li class="breadcrumb-item"><a href="{{ route('estate_owners.index') }}">Estate Owners</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Estate Owner Registration</h4>
                <p class="card-title-desc">Please fill in the required information below.</p>

                <!-- Display success message if present -->
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('estate_owners.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row mb-3">
                        <label for="fName" class="col-sm-2 col-form-label">First Name</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('fName') is-invalid @enderror" 
                                   type="text" 
                                   name="fName" 
                                   id="fName" 
                                   placeholder="Enter first name" 
                                   value="{{ old('fName') }}" 
                                   maxlength="160" 
                                   required>
                            @error('fName')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="lName" class="col-sm-2 col-form-label">Last Name</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('lName') is-invalid @enderror" 
                                   type="text" 
                                   name="lName" 
                                   id="lName" 
                                   placeholder="Enter last name" 
                                   value="{{ old('lName') }}" 
                                   maxlength="160" 
                                   required>
                            @error('lName')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="email" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('email') is-invalid @enderror" 
                                   type="email" 
                                   name="email" 
                                   id="email" 
                                   placeholder="Enter email" 
                                   value="{{ old('email') }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="phones" class="col-sm-2 col-form-label">Phone Numbers</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('phones') is-invalid @enderror" 
                                   type="text" 
                                   name="phones" 
                                   id="phones" 
                                   placeholder="Enter phone numbers (comma-separated)" 
                                   value="{{ old('phones') }}" 
                                   required>
                            @error('phones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="means_of_identification" class="col-sm-2 col-form-label">Identification Type</label>
                        <div class="col-sm-10">
                            <select class="form-select @error('means_of_identification') is-invalid @enderror" 
                                    name="means_of_identification" 
                                    id="means_of_identification" 
                                    required>
                                <option value="">Select identification type</option>
                                <option value="passport" {{ old('means_of_identification') == 'passport' ? 'selected' : '' }}>Passport</option>
                                <option value="nin" {{ old('means_of_identification') == 'nin' ? 'selected' : '' }}>NIN</option>
                                <option value="driver_licence" {{ old('means_of_identification') == 'driver_licence' ? 'selected' : '' }}>Driver's License</option>
                                <option value="nis" {{ old('means_of_identification') == 'nis' ? 'selected' : '' }}>NIS</option>
                            </select>
                            @error('means_of_identification')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="identification_image" class="col-sm-2 col-form-label">Identification Image</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('identification_image') is-invalid @enderror" 
                                   type="file" 
                                   name="identification_image" 
                                   id="identification_image" 
                                   accept="image/jpeg,image/png,image/jpg,application/pdf" 
                                   required>
                            <small class="form-text text-muted">Max size: 2MB. Accepted: JPEG, PNG, JPG, PDF</small>
                            @error('identification_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="address" class="col-sm-2 col-form-label">Address</label>
                        <div class="col-sm-10">
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      name="address" 
                                      id="address" 
                                      placeholder="Enter address" 
                                      rows="3" 
                                      maxlength="190" 
                                      required>{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="next_of_kin" class="col-sm-2 col-form-label">Next of Kin</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('next_of_kin') is-invalid @enderror" 
                                   type="text" 
                                   name="next_of_kin" 
                                   id="next_of_kin" 
                                   placeholder="Enter next of kin" 
                                   value="{{ old('next_of_kin') }}" 
                                   maxlength="160" 
                                   required>
                            @error('next_of_kin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Added missing next_of_kin_phone field -->
                    <div class="row mb-3">
                        <label for="next_of_kin_phone" class="col-sm-2 col-form-label">Next of Kin Phone</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('next_of_kin_phone') is-invalid @enderror" 
                                   type="text" 
                                   name="next_of_kin_phone" 
                                   id="next_of_kin_phone" 
                                   placeholder="Enter next of kin phone number" 
                                   value="{{ old('next_of_kin_phone') }}" 
                                   required>
                            @error('next_of_kin_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="bank_name" class="col-sm-2 col-form-label">Bank Name</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('bank_name') is-invalid @enderror" 
                                   type="text" 
                                   name="bank_name" 
                                   id="bank_name" 
                                   placeholder="Enter bank name" 
                                   value="{{ old('bank_name') }}" 
                                   maxlength="160" 
                                   required>
                            @error('bank_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="account_number" class="col-sm-2 col-form-label">Account Number</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('account_number') is-invalid @enderror" 
                                   type="text" 
                                   name="account_number" 
                                   id="account_number" 
                                   placeholder="Enter 10-digit account number" 
                                   value="{{ old('account_number') }}" 
                                   maxlength="10" 
                                   pattern="\d{10}" 
                                   required>
                            @error('account_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-10 offset-sm-2">
                            <a href="{{ route('estate_owners.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection