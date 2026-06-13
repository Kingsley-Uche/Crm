@extends('layouts.dashboard.landpage')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Edit Invoice</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('invoice.index') }}">Invoices</a>
                    </li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>
{{-- Validation Errors --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <h5 class="mb-2">
            <i class="fas fa-exclamation-triangle"></i>
            Please correct the following errors:
        </h5>

        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Flash Error --}}
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

{{-- Flash Success --}}
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<form method="POST" action="{{ route('invoice.update', $invoice->id) }}">
    @csrf
    @method('PUT')

    <div class="card">
        <div class="card-body">

            <h4 class="card-title mb-4">Invoice Information</h4>

            <div class="row">

                {{-- READ ONLY INFO --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Invoice Ref</label>
                    <input type="text" class="form-control" value="{{ $invoice->invoice_ref }}" readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Tenant</label>
                    <input type="text" class="form-control"
                           value="{{ $invoice->tenant->full_name ?? 'N/A' }}" readonly>
                </div>
                <input type ='hidden' name ='tenant_id' value ="{{$invoice->tenant_id}}">
                  <input type ='hidden' name ='apartment_id' value ="{{$invoice->apartment_id}}">
                   <input type ='hidden' name ='location_id' value ="{{$invoice->location_id}}">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Apartment</label>
                    <input type="text" class="form-control"
                           value="{{ $invoice->apartment->unique_code ?? 'N/A' }}" readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <input type="text" class="form-control"
                           value="{{ ucfirst($invoice->status) }}" readonly>
                </div>

                {{-- EDITABLE FIELDS ONLY --}}
                <div class="col-md-6 mb-3">
    <label class="form-label">Paid Amount</label>
    <input type="number"
           name="paid_amount"
           class="form-control @error('paid_amount') is-invalid @enderror"
           value="{{ old('paid_amount', $invoice->paid_amount) }}"
           min="0">

    @error('paid_amount')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
               <div class="col-md-6 mb-3">
    <label class="form-label">Due Date</label>
    <input type="date"
           name="due_date"
           class="form-control @error('due_date') is-invalid @enderror"
           value="{{ old('due_date', $invoice->due_date) }}">

    @error('due_date')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
                <div class="col-md-12 mb-3">
    <label class="form-label">Description</label>
    <textarea name="description"
              class="form-control @error('description') is-invalid @enderror"
              rows="3">{{ old('description', $invoice->description) }}</textarea>

    @error('description')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

            </div>
        </div>
    </div>

    {{-- ITEMS (READ ONLY - SAFE ACCOUNTING PRACTICE) --}}
    @php
    $items = old('items');

    if (!$items) {
        $items = $invoice->paymentListings->map(function ($item) {
            return [
                'name' => $item->name,
                'qty' => $item->qty,
                'unit_charge' => $item->unit_charge,
                'amount' => $item->amount,
            ];
        })->toArray();
    }

    if (empty($items)) {
        $items[] = [
            'name' => '',
            'qty' => 1,
            'unit_charge' => '',
            'amount' => ''
        ];
    }
@endphp

@php
    $existingTotal = $invoice->paymentListings->sum('amount');
    $newItems = old('new_items', []);
@endphp

<div class="card">
    <div class="card-body">

        <div class="d-flex justify-content-between mb-3">
            <h4 class="card-title">Invoice Items</h4>

            <button type="button"
                    class="btn btn-primary"
                    id="addItem">
                Add Item
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">

                <thead>
                    <tr>
                        <th width="35%">Item Name</th>
                        <th width="15%">Qty</th>
                        <th width="20%">Unit Charge</th>
                        <th width="20%">Amount</th>
                        <th width="10%">Action</th>
                    </tr>
                </thead>

                {{-- Existing Database Items --}}
                <tbody>
                    @foreach($invoice->paymentListings as $item)
                    <tr class="existing-item">

                        <td>
                            <input type="text"
                                   class="form-control"
                                   value="{{ $item->name }}"
                                   readonly>
                        </td>

                        <td>
                            <input type="number"
                                   class="form-control"
                                   value="{{ $item->qty }}"
                                   readonly>
                        </td>

                        <td>
                            <input type="number"
                                   class="form-control"
                                   value="{{ $item->unit_charge }}"
                                   readonly>
                        </td>

                        <td>
                            <input type="number"
                                   class="form-control existing-amount"
                                   value="{{ $item->amount }}"
                                   readonly>
                        </td>

                        <td>
                            <span class="badge bg-secondary">
                                Existing
                            </span>
                        </td>

                    </tr>
                    @endforeach

                    <tr>
                        <td colspan="5"
                            class="bg-light text-center fw-bold">
                            New Items
                        </td>
                    </tr>
                </tbody>

                {{-- New Items --}}
                <tbody id="invoiceItems">

                    @foreach($newItems as $i => $item)
                    <tr class="new-item">

                        <td>
                            <input type="text"
                                   name="new_items[{{ $i }}][name]"
                                   class="form-control"
                                   value="{{ $item['name'] ?? '' }}"
                                   required>
                        </td>

                        <td>
                            <input type="number"
                                   min="1"
                                   name="new_items[{{ $i }}][qty]"
                                   class="form-control qty"
                                   value="{{ $item['qty'] ?? 1 }}"
                                   required>
                        </td>

                        <td>
                            <input type="number"
                                   step="0.01"
                                   min="0"
                                   name="new_items[{{ $i }}][unit_charge]"
                                   class="form-control unit_charge"
                                   value="{{ $item['unit_charge'] ?? '' }}"
                                   required>
                        </td>

                        <td>
                            <input type="number"
                                   step="0.01"
                                   name="new_items[{{ $i }}][amount]"
                                   class="form-control amount"
                                   value="{{ $item['amount'] ?? '' }}"
                                   readonly>
                        </td>

                        <td>
                            <button type="button"
                                    class="btn btn-danger removeRow">
                                Remove
                            </button>
                        </td>

                    </tr>
                    @endforeach

                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">
                            Grand Total
                        </th>

                        <th>
                            <input type="text"
                                   id="grandTotal"
                                   name="amount"
                                   class="form-control"
                                   readonly>
                        </th>

                        <th></th>
                    </tr>
                </tfoot>

            </table>
        </div>

    </div>
</div>
  

    {{-- SUBMIT --}}
    <div class="text-end mt-3">
        <button type="submit" class="btn btn-success">
            Update Invoice
        </button>
    </div>

</form>

@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {

    const invoiceItems = document.getElementById('invoiceItems');
    const grandTotal = document.getElementById('grandTotal');

    let rowIndex = {{ count($newItems) }};

    document.getElementById('addItem').addEventListener('click', function () {

        const row = `
            <tr class="new-item">

                <td>
                    <input type="text"
                           name="new_items[${rowIndex}][name]"
                           class="form-control"
                           required>
                </td>

                <td>
                    <input type="number"
                           min="1"
                           value="1"
                           name="new_items[${rowIndex}][qty]"
                           class="form-control qty"
                           required>
                </td>

                <td>
                    <input type="number"
                           min="0"
                           step="0.01"
                           name="new_items[${rowIndex}][unit_charge]"
                           class="form-control unit_charge"
                           required>
                </td>

                <td>
                    <input type="number"
                           step="0.01"
                           name="new_items[${rowIndex}][amount]"
                           class="form-control amount"
                           readonly>
                </td>

                <td>
                    <button type="button"
                            class="btn btn-danger removeRow">
                        Remove
                    </button>
                </td>

            </tr>
        `;

        invoiceItems.insertAdjacentHTML('beforeend', row);

        rowIndex++;

        calculateTotals();
    });

    document.addEventListener('click', function (e) {

        if (!e.target.classList.contains('removeRow')) {
            return;
        }

        e.target.closest('tr').remove();

        calculateTotals();
    });

    document.addEventListener('input', function (e) {

        if (
            e.target.classList.contains('qty') ||
            e.target.classList.contains('unit_charge')
        ) {
            calculateTotals();
        }
    });

    function calculateTotals() {

        let existingTotal = 0;
        let newTotal = 0;

        document.querySelectorAll('.existing-amount').forEach(function (input) {

            existingTotal += parseFloat(input.value) || 0;

        });

        document.querySelectorAll('#invoiceItems tr').forEach(function (row) {

            const qty = parseFloat(
                row.querySelector('.qty')?.value
            ) || 0;

            const unitCharge = parseFloat(
                row.querySelector('.unit_charge')?.value
            ) || 0;

            const amount = qty * unitCharge;

            const amountInput = row.querySelector('.amount');

            if (amountInput) {
                amountInput.value = amount.toFixed(2);
            }

            newTotal += amount;
        });

        grandTotal.value = (existingTotal + newTotal).toFixed(2);
    }

    calculateTotals();
});
</script>