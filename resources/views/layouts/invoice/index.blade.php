@extends('layouts.dashboard.landpage')



@section('content')

<style>
    th, td {
        font-size: 12px;
    }
</style>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 px-1">Invoices</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Invoices</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="card-title">Invoice List</h4>
                        <p class="card-title-desc">
                            View all generated invoices and manage payments.
                        </p>
                    </div>

                    <div>
                        <a href="{{ route('invoice.create') }}" class="btn btn-success">
                            Create Invoice
                        </a>
                    </div>
                </div>

                <table id="selection-datatable" class="table dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Invoice Ref</th>
                            <th>Tenant</th>
                            <th>Apartment</th>
                            <th>Amount</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th>Created</th>
                            <th>Options</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($invoices as $index => $invoice)
                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td>{{ $invoice->invoice_ref }}</td>

                                <td>
                                    {{ optional($invoice->tenant)->full_name ?? 'N/A' }}
                                </td>

                                <td>
                                    {{ optional($invoice->apartment)->name ?? 'N/A' }}
                                </td>

                                <td>{{ number_format($invoice->amount, 2) }}</td>
                                <td>{{ number_format($invoice->paid_amount, 2) }}</td>
                                <td>{{ number_format($invoice->balance, 2) }}</td>

                                <td>
                                    @php
                                        $status = $invoice->status;
                                    @endphp

                                    <span class="badge bg-{{ $status == 'paid' ? 'success' : ($status == 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>

                                <td>
                                    {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d M, Y') : 'N/A' }}
                                </td>

                                <td>
                                    {{ $invoice->created_at->format('d M, Y') }}
                                </td>

                                <td>
                                    <a href="{{ route('invoice.show', $invoice->id) }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                        @if($invoice->status ==='pending'||$invoice->status ==='partially_paid' )
                                    <a href="{{ route('invoice.edit', $invoice->id) }}">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                        @endif
                                    <form action="{{ route('invoice.destroy', $invoice->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')

                                        <!-- <button type="submit" class="btn btn-sm delete-btn"
                                            data-info="{{ $invoice->invoice_ref }}">
                                            <i class="fas fa-trash-alt text-danger"></i>
                                        </button> -->
                                    </form>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>

                </table>

            </div>
        </div>
    </div>
</div>

@endsection