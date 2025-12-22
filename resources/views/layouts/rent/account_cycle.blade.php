@extends('layouts.dashboard.landpage')

@section('content')
<style>
    th, td { font-size: 12px; }
    .table-responsive-flex { display: block; width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .badge { display: inline-block; max-width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-transform: capitalize; font-weight: 300; }
    #apartment_name { text-transform: capitalize; }
</style>

<!-- Flash Message -->
@if (session('success'))
<div class="row">
    <div class="col-12">
        <div class="alert alert-success m-3">{{ session('success') }}</div>
    </div>
</div>
@endif

<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 px-1">Rent Renewal</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="#">Rent</a></li>
                    <li class="breadcrumb-item active">Renewal</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Rent Accounts Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Active Rents</h4>
                <p class="card-title-desc">Click the rent account to renew</p>
                <div class="table-responsive-flex">
                    <table class="table table-striped table-bordered dt-responsive nowrap" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Address</th>
                                <th>Current Rent Start</th>
                                <th>Current Rent Due</th>
                                <th>Property Ref</th>
                                <th>Tenant Name</th>
                                <th>Email</th>
                                <th>Account Type</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                    @foreach ($rent_accounts as $account)
    <tr>
        <td>{{ ucwords(optional($account->apartment)->address ?? '-') }}</td>

        <td>
            {{ optional(optional($account->rentCycles)->last())->start_date
                ? \Carbon\Carbon::parse($account->rentCycles->last()->start_date)
                    ->timezone('Europe/London')
                    ->format('Y-m-d h:i A')
                : '-' }}
        </td>

        <td>
            {{ optional(optional($account->rentCycles)->last())->end_date
                ? \Carbon\Carbon::parse($account->rentCycles->last()->end_date)
                    ->timezone('Europe/London')
                    ->format('Y-m-d h:i A')
                : '-' }}
        </td>

        <td>{{ optional($account->apartment)->property_ref ?? '-' }}</td>

        <td>
            {{ trim(($account->tenant->full_name ?? '-')) }}
        </td>

        <td>{{ $account->tenant->occupant_email ?? '-' }}</td>
        
        <td>{{ $account->account_type ?? '-' }}</td>

        <td>
            <div class="d-flex gap-1">
                <button class="btn btn-rounded btn-success renew-btn btn-sm"
                    title="Renew this apartment"
                    data-apartment-id="{{ $account->apartment_id }}"
                    data-rent-account-id="{{ $account->id }}"
                    data-apartment-address="{{ ucwords(optional($account->apartment)->address ?? '-') }}"
                    data-tenant-id="{{ optional($account->tenant)->id }}"
                    data-account-type="{{ $account->account_type }}"
                    data-fee="{{ optional(optional($account->rentCycles)->last())->rent_fee ?? 0 }}">
                    <i class="fa fa-sync text-white"></i> Renew Rent
                </button>
            </div>
        </td>
    </tr>
@endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Renewal Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3">
            <div class="modal-header bg-success">
                <h5 class="modal-title text-white p-2 text-center">
                    Renew Apartment: <span id="apartment_name"></span>
                </h5>
            </div>
            <form id="RenewRent" method="POST" action="{{ route('rent.renew') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rent_fee" class="form-label">Rent Fee</label>
                        <small class='text-danger'>*You can change the rent fee here if need be.*</small>
                        <input class="form-control" type="number" id="rent_fee" name="rent_fee" step="0.01" min="0" placeholder="0.00" required>
                    </div>
                     <div class="mb-3">
                        <label for="payment_made" class="form-label">Payment Made</label>
                        <small class='text-danger'>*Enter exact amount paid by tenant.*</small>
                        <input class="form-control" type="number" id="payment_made" name="payment_made" step="0.01" min="0" placeholder="0.00" required>
                    </div>
                    <div class="mb-3">
                        <label for="start-date-input" class="form-label">Start Date</label>
                        <input class="form-control" type="date" id="start-date-input" name="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="end-date-input" class="form-label">End Date</label>
                        <input class="form-control" type="date" id="end-date-input" name="end_date" required>
                    </div>
                    <input type="hidden" id="apartment_id" name="apartment_id">
                    <input type="hidden" id="rent_account_id" name="rent_account_id">
                    <input type="hidden" id="apartment_address" name="apartment_address">
                    <input type="hidden" id="tenant_id" name="tenant_id">
                    <input type="hidden" id="account_type" name="account_type">
                </div>
                     {{-- Escalation Policy Dropdown --}}
                        <div class="col-mb-">
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
                        <div class="col-mb-3">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" id="manage">Confirm Renewal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const startDateInput = document.getElementById('start-date-input');
    const endDateInput = document.getElementById('end-date-input');
    const rentFeeInput = document.getElementById('rent_fee');
    const today = new Date().toISOString().split('T')[0];

    startDateInput.setAttribute('min', today);
    endDateInput.setAttribute('min', new Date(Date.now() + 86400000).toISOString().split('T')[0]);

    startDateInput.addEventListener('change', function () {
        const nextDay = new Date(this.value);
        nextDay.setDate(nextDay.getDate() + 1);
        endDateInput.setAttribute('min', nextDay.toISOString().split('T')[0]);
    });

    document.querySelectorAll('.renew-btn').forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('apartment_id').value = this.dataset.apartmentId;
            document.getElementById('rent_account_id').value = this.dataset.rentAccountId;
            document.getElementById('apartment_address').value = this.dataset.apartmentAddress;
            document.getElementById('tenant_id').value = this.dataset.tenantId;
            document.getElementById('apartment_name').innerText = this.dataset.apartmentAddress;
             document.getElementById('account_type').value = this.dataset.accountType;
            rentFeeInput.value = parseFloat(this.dataset.fee).toFixed(2);

            const bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));
            bookingModal.show();
        });
    });

    const renewForm = document.getElementById('RenewRent');
    renewForm.addEventListener('submit', function (event) {
        event.preventDefault();

        const submitButton = document.getElementById('manage');
        submitButton.setAttribute('disabled', 'true');
        const formData = new FormData(renewForm);

        fetch(renewForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            submitButton.removeAttribute('disabled');

            const bookingModal = bootstrap.Modal.getInstance(document.getElementById('bookingModal'));
            bookingModal.hide();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Renewal Confirmed!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => window.location.reload());
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Renewal Failed!',
                    text: data.message || 'An error occurred.',
                });
            }
        })
        .catch(error => {
            console.log(error);
            submitButton.removeAttribute('disabled');
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An error occurred while processing renewal.',
            });
        });
    });
 });
</script>
@endsection
