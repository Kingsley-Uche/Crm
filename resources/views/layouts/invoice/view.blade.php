@extends('layouts.dashboard.landpage')

@section('styles')
<style>
    :root {
        --brand-color: {{ $brand_data->brand_color ?? '#2a3042' }};
        --brand-light: rgba(42, 48, 66, 0.04);
    }

    /* Screen display constraints matching A4 proportions */
    .invoice-card {
        border: none !important;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.03) !important;
        border-radius: 12px !important;
        max-width: 210mm !important; /* Standard A4 Width */
        margin: 20px auto !important;
        background: #ffffff !important;
    }

    .invoice-title-section {
        padding: 5px 0;
    }

    .invoice-brand-logo {
        max-height: 48px !important;
        max-width: 160px !important;
        object-fit: contain !important;
        margin-bottom: 8px !important;
        display: block !important;
    }

    .invoice-meta-title {
        font-weight: 800 !important;
        letter-spacing: 0.5px;
        color: var(--brand-color) !important;
        font-size: 22px !important;
    }

    /* Compact Information Blocks to save vertical layout space */
    .address-card {
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 6px !important;
        padding: 12px 14px !important;
        height: 100%;
        box-sizing: border-box !important;
    }

    .address-card strong {
        color: #475569 !important;
        font-size: 10px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        display: block !important;
        margin-bottom: 4px !important;
    }

    .address-card p {
        color: #1e293b !important;
        font-size: 13px !important;
        line-height: 1.5 !important;
    }

    .invoice-table-wrapper {
        border-radius: 6px !important;
        overflow: hidden !important;
        border: 1px solid #e2e8f0 !important;
    }

    .table-custom {
        margin-bottom: 0 !important;
    }

    .table-custom thead th {
        background-color: #f1f5f9 !important;
        color: #475569 !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        font-size: 11px !important;
        letter-spacing: 0.5px !important;
        padding: 10px 14px !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }

    .table-custom tbody td {
        padding: 10px 14px !important;
        font-size: 13px !important;
        color: #334155 !important;
        vertical-align: middle !important;
        border-bottom: 1px solid #edf2f7 !important;
    }

    .totals-row td {
        border: none !important;
        padding: 5px 14px !important;
    }

    .grand-total-row {
        border-top: 1px solid #e2e8f0 !important;
    }

    .grand-total-row td {
        padding-top: 10px !important;
        font-size: 15px !important;
        color: var(--brand-color) !important;
    }

    .badge-status {
        display: inline-block !important;
        padding: 4px 12px !important;
        font-size: 10px !important;
        font-weight: 700 !important;
        border-radius: 50px !important;
        text-transform: uppercase !important;
    }
    .badge-paid { background-color: #def7ec !important; color: #03543f !important; }
    .badge-pending { background-color: #fef3c7 !important; color: #92400e !important; }
    .badge-unpaid { background-color: #fde8e8 !important; color: #9b1c1c !important; }

    .invoice-footer {
        margin-top: 40px !important;
        text-align: center !important;
        border-top: 1.5px solid var(--brand-color) !important;
        padding-top: 16px !important;
        page-break-inside: avoid !important;
    }

    .invoice-footer .brand-signature {
        color: var(--brand-color) !important;
        font-weight: 700 !important;
        font-size: 14px !important;
        margin-bottom: 2px !important;
    }

    .invoice-footer .brand-details {
        color: #64748b !important;
        font-size: 11px !important;
        line-height: 1.4 !important;
    }

    /* Strict A4 Print-specific overrides */
    @media print {
        @page {
            size: A4 !important;
            margin: 12mm 15mm 12mm 15mm !important; /* Clean margins to avoid cutoff */
        }
        
        body {
            background: #ffffff !important;
            color: #000000 !important;
            width: 100% !important;
        }

        .invoice-card {
            box-shadow: none !important;
            border: none !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        .card-body {
            padding: 0 !important; /* Maximizes absolute usable area */
        }

        .address-card {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .table-custom thead th {
            background-color: #f1f5f9 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .invoice-footer {
            border-top-color: var(--brand-color) !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Block components from generating multi-page orphans or splitting in half */
        .address-card, .invoice-table-wrapper, .invoice-footer {
            page-break-inside: avoid !important;
        }

        .page-title-box, .breadcrumb, .d-print-none {
            display: none !important;
        }
    }
</style>
@endsection

@section('content')
            <div class="row d-print-none">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Invoice Details</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Utility</a></li>
                                <li class="breadcrumb-item active">Invoice</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card invoice-card">
                        <div class="card-body p-4 p-sm-5">

                            {{-- INVOICE TOP BAR WITH LOGO --}}
                            <div class="row align-items-center border-bottom pb-3 mb-4">
                                <div class="col-6">
                                    <div class="invoice-title-section">
                                        @if(!empty($brand_data->logo_ur))
                                            <img src="{{ asset($brand_data->logo_ur) }}" alt="{{ $brand_data->name ?? 'Brand Logo' }}" class="invoice-brand-logo">
                                        @else
                                            <h3 class="invoice-meta-title m-0 mb-1">{{ $brand_data->name ?? 'INVOICE' }}</h3>
                                        @endif
                                        
                                        @if(isset($brand_data->address))
                                            <p class="text-muted small m-0" style="font-size: 12px; max-width: 85%;">{{ $brand_data->address }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6 text-end">
                                    <h4 class="font-size-15 mb-1" style="font-size: 15px;">
                                        <strong>Invoice #:</strong> <span class="text-muted">{{ $invoice->invoice_ref }}</span>
                                    </h4>
                                    <div>
                                        <span class="badge-status badge-{{ strtolower($invoice->status) }}">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- META INFORMATION GRID BLOCKS --}}
                            <div class="row g-3 mb-4">
                                {{-- BILL TO --}}
                                <div class="col-3">
                                    <div class="address-card">
                                        <strong>Billed To</strong>
                                        <p class="m-0">
                                            <span class="fw-semibold text-dark">{{ $invoice->tenant->full_name ?? 'N/A' }}</span><br>
                                            <span class="text-muted small d-block text-truncate">{{ $invoice->tenant->occupant_email ?? '' }}</span>
                                            <span class="text-muted small d-block">{{ $invoice->tenant->mobile_number ?? '' }}</span>
                                        </p>
                                    </div>
                                </div>

                                {{-- PROPERTY --}}
                                <div class="col-3">
                                    <div class="address-card">
                                        <strong>Property Allocation</strong>
                                        <p class="m-0 text-muted small">
                                            <span class="text-dark fw-semibold">Unit:</span> {{ $invoice->apartment->unique_code ?? 'N/A' }}<br>
                                            <span class="text-dark fw-semibold">Loc:</span> {{ $invoice->location->name ?? 'N/A' }}<br>
                                            <span class="text-dark fw-semibold">Branch:</span> {{ $invoice->branch->name ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                {{-- ORDER DATE --}}
                                <div class="col-3">
                                    <div class="address-card">
                                        <strong>Issue Date</strong>
                                        <p class="m-0 fw-semibold text-dark">
                                            {{ $invoice->created_at->format('d M, Y') }}
                                        </p>
                                        <span class="text-muted small d-block">Time: {{ $invoice->created_at->format('h:i A') }}</span>
                                    </div>
                                </div>

                                {{-- DUE DATE --}}
                                <div class="col-3">
                                    <div class="address-card">
                                        <strong>Due Date</strong>
                                        <p class="m-0 fw-semibold {{ $invoice->due_date && \Carbon\Carbon::parse($invoice->due_date)->isPast() && strtolower($invoice->status) != 'paid' ? 'text-danger' : 'text-dark' }}">
                                            {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d M, Y') : 'Upon Receipt' }}
                                        </p>
                                        <span class="text-muted small d-block">Payment Window</span>
                                    </div>
                                </div>
                            </div>

                            {{-- ORDER SUMMARY TABLE --}}
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="mb-2">
                                        <h5 class="font-size-14 mb-0 text-dark fw-bold">Line Items Summary</h5>
                                    </div>

                                    <div class="table-responsive invoice-table-wrapper">
                                        <table class="table table-custom align-middle">
                                            <thead>
                                                <tr>
                                                    <th style="width: 50px;" class="text-center">#</th>
                                                    <th>Item Description</th>
                                                    <th class="text-center" style="width: 120px;">Unit Price</th>
                                                    <th class="text-center" style="width: 90px;">Quantity</th>
                                                    <th class="text-end" style="width: 140px;">Total (₦)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($invoice->paymentListings as $index => $item)
                                                    <tr>
                                                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                                                        <td>
                                                            <span class="fw-semibold text-dark">{!! $item->name !!}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            {{ number_format($item->unit_charge, 2) }}
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-light text-dark px-2 py-1">{{ $item->qty }}</span>
                                                        </td>
                                                        <td class="text-end fw-semibold">
                                                            {{ number_format($item->amount, 2) }}
                                                        </td>
                                                    </tr>
                                                @endforeach

                                                <tr class="totals-row">
                                                    <td colspan="3"></td>
                                                    <td class="text-center text-muted fw-semibold">Subtotal</td>
                                                    <td class="text-end text-muted fw-semibold">{{ number_format($invoice->amount, 2) }}</td>
                                                </tr>
                                                <tr class="totals-row">
                                                    <td colspan="3"></td>
                                                    <td class="text-center text-success fw-semibold">Amount Paid</td>
                                                    <td class="text-end text-success fw-semibold">- {{ number_format($invoice->paid_amount, 2) }}</td>
                                                </tr>
                                                <tr class="totals-row grand-total-row">
                                                    <td colspan="3"></td>
                                                    <td class="text-center fw-bold">Balance Due</td>
                                                    <td class="text-end fw-bold">
                                                        ₦ {{ number_format($invoice->balance, 2) }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- BRAND DATA FOOTER COMPONENT --}}
                                    <div class="invoice-footer">
                                        <div class="brand-signature">
                                            Thank you for choosing {{ $brand_data->name ?? 'us' }}!
                                        </div>
                                        <div class="brand-details">
                                            @if(!empty($brand_data->address)) <span>{{ $brand_data->address }}</span> @endif
                                            @if(!empty($brand_data->contact_phone) || !empty($brand_data->contact_email))
                                                <br>
                                                <span>{{ $brand_data->contact_phone ?? '' }}</span> 
                                                @if(!empty($brand_data->contact_phone) && !empty($brand_data->contact_email)) | @endif 
                                                <span>{{ $brand_data->contact_email ?? '' }}</span>
                                            @endif
                                            @if(!empty($brand_data->website_url))
                                                <br><span>{{ $brand_data->website_url }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- ACTION BUTTONS --}}
                                    <div class="d-print-none mt-4">
                                        <div class="float-end">
                                            <a href="javascript:window.print()" class="btn btn-light waves-effect px-4 me-2">
                                                <i class="fa fa-print me-2"></i> Print / PDF
                                            </a>
                                            <a href="#" class="btn btn-primary waves-effect waves-light px-4" style="background-color: var(--brand-color); border-color: var(--brand-color);">
                                                <i class="fa fa-paper-plane me-2"></i> Send Invoice
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
@endsection