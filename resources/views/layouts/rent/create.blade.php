@extends('layouts.dashboard.landpage')

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<style>
    /* 1. Highlighted dropdown option (when navigating via keyboard or hover) */
.select2-container--default .select2-results__option--highlighted[aria-selected] {
  background-color: #198754 !important;  /* Bootstrap “success” */
  color: #fff !important;
}

/* 2. Selected value text (single-select) */
.select2-container--default .select2-selection--single .select2-selection__rendered {
  color: #198754;        /* text in box becomes green */
  font-weight: 600;
}

/* 3. Placeholder text (optional)—make it slightly muted */
.select2-container--default .select2-selection--single .select2-selection__placeholder {
  color: rgba(25, 135, 84, 0.5);
}

/* 4. Remove the default blue border when focused; use green instead */
.select2-container--default .select2-selection--single:focus,
.select2-container--default .select2-selection--single:focus .select2-selection__rendered {
  border-color: #198754 !important;
  box-shadow: 0 0 0 0.1rem rgba(25, 135, 84, 0.25);
}

/* 5. “X” (clear) icon for single-select—make it green */
.select2-container--default .select2-selection--single .select2-selection__clear {
  color: #198754;
}

/* 6. Multi-select “choice” pills (if you ever use select2[multiple]) */
.select2-container--default .select2-selection--multiple .select2-selection__choice {
  background-color: #198754 !important;
  border-color: #198754 !important;
  color: #fff !important;
}

</style>

<div class="row">
    <div class="col-12">
        {{-- Display session messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mx-2" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mx-2" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Display validation errors --}}
        @if ($errors->any())
            <div class="alert alert-danger mx-2">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 px-1">Create Rent Account</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('apartments.view') }}">Rent Accounts</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="card shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h6 class="text-center text-muted">New Rent Account Form</h6>

                <form method="POST" action="{{ route('rent.store') }}">
                    @csrf

                    <div class="row g-3">
                        {{-- Tenant Select --}}
                        <div class="col-md-6 col-xl-4">
                            <label for="tenant_id">Tenant <span class="text-danger">*</span></label>
                            <select name="tenant_id"
                                    id="tenant_id"
                                    class="form-select select2 @error('tenant_id') is-invalid @enderror"
                                    required>
                                <option value="">— Select Tenant —</option>
                                @foreach($tenants as $tenant)
                                    <option value="{{ $tenant->id }}"
                                        {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                        {{ ucwords($tenant->first_name . ' ' . $tenant->last_name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tenant_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Apartment Select --}}
                        <div class="col-md-6 col-xl-4">
                            <label for="apartment_id">Apartment <span class="text-danger">*</span></label>
                            <select name="apartment_id"
                                    id="apartment_id"
                                    class="form-select select2 @error('apartment_id') is-invalid @enderror"
                                    required>
                                <option value="">— Select Apartment —</option>
                                @foreach($apartments as $apt)
                                    <option value="{{ $apt->id }}"
                                        {{ old('apartment_id') == $apt->id ? 'selected' : '' }}>
                                        Type: {{ $apt->shelter->name ?? 'Unknown Shelter' }}
                                        ─ Address: {{ $apt->address }}
                                    </option>
                                @endforeach
                            </select>
                            @error('apartment_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Unit Number (read-only) --}}
                        <div class="col-md-6 col-xl-4">
                            <label for="unit_number">Unit Number</label>
                            <input type="text"
                                   name="unit_number"
                                   id="unit_number"
                                   value="{{ old('unit_number') }}"
                                   class="form-control @error('unit_number') is-invalid @enderror"
                                   readonly />
                            @error('unit_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Start Date --}}
                        <div class="col-md-6 col-xl-4">
                            <label for="start_date">Start Date <span class="text-danger">*</span></label>
                            <input type="date"
                                   name="start_date"
                                   id="start_date"
                                   value="{{ old('start_date') }}"
                                   class="form-control @error('start_date') is-invalid @enderror"
                                   required />
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- First Cycle End Date --}}
                        <div class="col-md-6 col-xl-4">
                            <label for="end_date">First Cycle End Date <span class="text-danger">*</span></label>
                            <input type="date"
                                   name="end_date"
                                   id="end_date"
                                   value="{{ old('end_date') }}"
                                   class="form-control @error('end_date') is-invalid @enderror"
                                   required />
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Rent Fee --}}
                        <div class="col-md-6 col-xl-4">
                            <label for="rent_fee">Expected Rent(£) <span class="text-danger">*</span></label>
                            <input type="number"
                                   step="0.01"
                                   name="rent_fee"
                                   id="rent_fee"
                                   value="{{ old('rent_fee') }}"
                                   class="form-control @error('rent_fee') is-invalid @enderror"
                                   required />
                            @error('rent_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <label for="rent_fee">Payment Received(£) <span class="text-danger">*</span></label>
                            <input type="number"
                                   step="0.01"
                                   name="payment_made"
                                   id="rent_fee"
                                   value="{{ old('payment_made') }}"
                                   class="form-control @error('payment_made') is-invalid @enderror"
                                   required />
                            @error('payment_made')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Account Type Dropdown --}}
                        <div class="col-md-6 col-xl-4">
                            <label for="account_type">Account Type <span class="text-danger">*</span></label>
                            <select name="account_type"
                                    id="account_type"
                                    class="form-select @error('account_type') is-invalid @enderror"
                                    required>
                                <option value="">— Select Account Type —</option>
                                <option value="REN"   {{ old('account_type') == 'REN'   ? 'selected' : '' }}>REN</option>
                                <option value="LEASE" {{ old('account_type') == 'LEASE' ? 'selected' : '' }}>LEASE</option>
                                <option value="SUB"   {{ old('account_type') == 'SUB'   ? 'selected' : '' }}>SUBLET</option>
                            </select>
                            @error('account_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Escalation Policy Dropdown --}}
                        <div class="col-md-6 col-xl-4">
                            <label for="escalation_policy">Escalation Policy <span class="text-danger">*</span></label>
                            <select name="escalation_policy"
                                    id="escalation_policy"
                                    class="form-select @error('escalation_policy') is-invalid @enderror"
                                    required>
                                <option value="">— Select Policy —</option>
                                <option value="STANDARD"      {{ old('escalation_policy') == 'STANDARD'      ? 'selected' : '' }}>STANDARD</option>
                                <option value="SECURE"        {{ old('escalation_policy') == 'SECURE'        ? 'selected' : '' }}>SECURE</option>
                                <option value="SECURE2"       {{ old('escalation_policy') == 'SECURE2'       ? 'selected' : '' }}>SECURE2</option>
                                <option value="LEGAL_NOTICE"  {{ old('escalation_policy') == 'LEGAL_NOTICE'  ? 'selected' : '' }}>LEGAL NOTICE</option>
                            </select>
                            @error('escalation_policy')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Payment Method Dropdown (example) --}}
                        <div class="col-md-6 col-xl-4">
                            <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method"
                                    id="payment_method"
                                    class="form-select @error('payment_method') is-invalid @enderror"
                                    required>
                                <option value="">— Select Payment Method —</option>
                                <option value="DD"       {{ old('payment_method') == 'DD'       ? 'selected' : '' }}>Direct Debit</option>
                                <option value="BANK"     {{ old('payment_method') == 'BANK'     ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="ONLINE"   {{ old('payment_method') == 'ONLINE'   ? 'selected' : '' }}>Online Card</option>
                                <option value="CASH"     {{ old('payment_method') == 'CASH'     ? 'selected' : '' }}>Cash</option>
                                 <option value="NONE"     {{ old('payment_method') == 'NONE'     ? 'selected' : '' }}>No Payment</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                           <div class='bg-light text-center rounded'>
                               <small class='text-danger'>**You should create an account once as the system would handle subsequent cycles automatically.</small>
                           </div>
                        {{-- Submit Button --}}
                        <div class="col-12 text-end mt-3">
                            <button type="submit" class="btn btn-success rounded">
                                Create Rent Account
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    {{-- jQuery (required for Select2) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- Select2 CSS/JS --}}
    <link
      href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
      rel="stylesheet"
    />
    <script
      src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"
    ></script>

    <script>
      $(document).ready(function() {
        // Initialize Select2 on tenant & apartment fields
        $('#tenant_id, #apartment_id').select2({
          width: '100%',
          placeholder: 'Select an option',
          allowClear: true
        });

        // Auto-fill Unit Number when apartment changes
        const apartments = @json($apartments);
        $('#apartment_id').on('change', function() {
          const selectedId = parseInt(this.value);
          const apt = apartments.find(a => a.id === selectedId);
          $('#unit_number').val(apt?.unit_number || '');
        });
      });
    </script>
@endsection
