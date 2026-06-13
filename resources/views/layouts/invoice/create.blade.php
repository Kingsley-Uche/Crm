@extends('layouts.dashboard.landpage')

<style>
.search-container {
    position: relative;
}

.search-input {
    margin-bottom: 5px;
}
</style>

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Create Invoice</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('invoice.index') }}">Invoices</a>
                    </li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div id="ajaxLoader" class="d-none">
    <div class="alert alert-info">
        <span class="spinner-border spinner-border-sm me-2"></span>
        Loading data...
    </div>
</div>

<form method="POST" action="{{ route('invoice.store') }}">
    @csrf

    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-4">Invoice Information</h4>

            <div class="row">
                <!-- Branch -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Branch</label>
                    <select name="branch_id" id="branch_id" class="form-select js-searchable" required>
                        <option value="">Select Branch</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Location -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Location</label>
                    <select name="location_id" id="location_id" class="form-select js-searchable" required>
                        <option value="">Select Location</option>
                    </select>
                </div>

                <!-- Apartment -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Apartment</label>
                    <select name="apartment_id" id="apartment_id" class="form-select js-searchable" required>
                        <option value="">Select Apartment</option>
                    </select>
                </div>

                <!-- Tenant -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tenant</label>
                    <select name="tenant_id" class="form-select js-searchable" required>
                        <option value="">Select Tenant</option>
                        @foreach($tenants as $tenant)
                            <option value="{{ $tenant->id }}">
                                {{ $tenant->full_name }} 
                                ({{ $tenant->occupant_email ?? '' }} - {{ $tenant->mobile_number ?? '' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Due Date -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date" class="form-control">
                </div>
                    <div class="col-md-6 mb-3">
                    <label class="form-label">Paid Amount</label>
                    <input type="number" name="paid_amount" class="form-control" min='0'>
                </div>

                <!-- Description -->
                <div class="col-md-12 mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3" 
                              placeholder="Invoice description"></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Items -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <h4 class="card-title">Invoice Items</h4>
                <button type="button" class="btn btn-primary" id="addItem">Add Item</button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="35%">Item Name</th>
                            <th width="15%">Qty</th>
                            <th width="20%">Unit Charge</th>
                            <th width="20%">Amount</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody id="invoiceItems">
                        <tr>
                            <td>
                                <input type="text" name="items[0][name]" class="form-control" required>
                            </td>
                            <td>
                                <input type="number" min="1" value="1" name="items[0][qty]" 
                                       class="form-control qty" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" name="items[0][unit_charge]" 
                                       class="form-control unit_charge" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="items[0][amount]" 
                                       class="form-control amount" readonly>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger removeRow">Remove</button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Grand Total</th>
                            <th>
                                <input type="text" id="grandTotal" name="amount" 
                                       class="form-control" readonly>
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-success">Create Invoice</button>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    let rowIndex = 1;

    const branchSelect = document.getElementById('branch_id');
    const locationSelect = document.getElementById('location_id');
    const apartmentSelect = document.getElementById('apartment_id');

    /* ====================== Loader ====================== */
    function showLoader() {
        document.getElementById('ajaxLoader').classList.remove('d-none');
    }

    function hideLoader() {
        document.getElementById('ajaxLoader').classList.add('d-none');
    }

    /* ====================== Branch -> Location ====================== */
    branchSelect.addEventListener('change', async function () {
        const branchId = this.value;

        // Reset dependent fields
        locationSelect.innerHTML = '<option value="">Select Location</option>';
        apartmentSelect.innerHTML = '<option value="">Select Apartment</option>';

        if (!branchId) return;

        try {
            showLoader();

            const response = await fetch(`{{ url('admin/invoice/locations') }}/${branchId}`);
            
            if (!response.ok) throw new Error('Failed to load locations');

            const data = await response.json();
            

            locationSelect.innerHTML = '<option value="">Select Location</option>';

            data.forEach(location => {
                
                const option = document.createElement('option');
                option.value = location.id;
                option.textContent = location.name;
                locationSelect.appendChild(option);
            });
            searchable.refresh(locationSelect);

        } catch (error) {
            console.error(error);
            locationSelect.innerHTML = '<option value="">Unable to load locations</option>';
        } finally {
            hideLoader();
        }
    });

    /* ====================== Location -> Apartments ====================== */
    locationSelect.addEventListener('change', async function () {
        const locationId = this.value;

        apartmentSelect.innerHTML = '<option value="">Select Apartment</option>';

        if (!locationId) return;

        try {
            showLoader();

            const response = await fetch(`{{ url('admin/invoice/apartments') }}/${locationId}`);

            if (!response.ok) throw new Error('Failed to load apartments');

            const data = await response.json();

            apartmentSelect.innerHTML = '<option value="">Select Apartment</option>';

            data.forEach(apartment => {
                var apartment_ref =apartment.property_ref;
                if(apartment_ref===null){
                    apartment_ref ="N/A";
                }
                const option = document.createElement('option');
                option.value = apartment.id;
                option.textContent = `${apartment_ref} - ${apartment.unique_code} - ${apartment.address}`;
                apartmentSelect.appendChild(option);
            });
            searchable.refresh(apartmentSelect);

        } catch (error) {
            console.error(error);
            apartmentSelect.innerHTML = '<option value="">Unable to load apartments</option>';
        } finally {
            hideLoader();
        }
    });

    /* ====================== Invoice Items ====================== */
    document.getElementById('addItem').addEventListener('click', function () {
        const row = `
            <tr>
                <td>
                    <input type="text" name="items[${rowIndex}][name]" class="form-control" required>
                </td>
                <td>
                    <input type="number" min="1" value="1" name="items[${rowIndex}][qty]" 
                           class="form-control qty" required>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="items[${rowIndex}][unit_charge]" 
                           class="form-control unit_charge" required>
                </td>
                <td>
                    <input type="number" step="0.01" name="items[${rowIndex}][amount]" 
                           class="form-control amount" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-danger removeRow">Remove</button>
                </td>
            </tr>
        `;

        document.getElementById('invoiceItems').insertAdjacentHTML('beforeend', row);
        rowIndex++;
    });

    /* Remove Row */
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('removeRow')) {
            const rows = document.querySelectorAll('#invoiceItems tr');
            if (rows.length > 1) {
                e.target.closest('tr').remove();
                calculateTotals();
            }
        }
    });

    /* Calculate Totals */
    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('qty') || e.target.classList.contains('unit_charge')) {
            calculateTotals();
        }
    });

    function calculateTotals() {
        let grandTotal = 0;

        document.querySelectorAll('#invoiceItems tr').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty').value) || 0;
            const fee = parseFloat(row.querySelector('.unit_charge').value) || 0;
            const amount = qty * fee;

            row.querySelector('.amount').value = amount.toFixed(2);
            grandTotal += amount;
        });

        document.getElementById('grandTotal').value = grandTotal.toFixed(2);
    }

    // Initial calculation
    calculateTotals();
});
</script>
@endsection