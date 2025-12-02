@php
    use Carbon\Carbon;
@endphp

@extends('layouts.dashboard.landpage')

@section('content')
<style>
    th, td {
        font-size: 12px;
    }
</style>

<!-- Success Message -->
<div class="row">
    <div class="col-12">
        <div class="card">
            @if (session('success'))
                <div class="alert alert-success m-3">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 px-1">Voids Report</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('voids.report') }}">Reports</a></li>
                    <li class="breadcrumb-item active">Voids</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- DataTable -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Voids Report</h4>
                <p class="card-title-desc">This table shows voids filtered by termination date.</p>

                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap"
                       style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Void Path</th>
                            <th>Classification</th>
                            <th>Property Ref</th>
                            <th>Address</th>
                            <th>Property Type</th>
                            <th>Property Subtype</th>
                            <th>Bedrooms</th>
                            <th>Void Status</th>
                            <th>HFI Code</th>
                            <th>UPRN</th>
                            <th>Ten Reason</th>
                            <th>VIN SCO Code</th>
                            <th>Updates</th>
                            <th>Previous Call Over</th>
                            <th>Void Ref</th>
                            <th>Termination Date</th>
                            <th>Ready for Let Date</th>
                            <th>Management Unit</th>
                            <th>Days Void</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($voids as $void)
                            <tr>
                                <td>{{ $void->void_path ?? 'N/A' }}</td>
                                <td>{{ $void->void_classification ?? 'N/A' }}</td>
                                <td>{{ $void->property_ref ?? 'N/A' }}</td>
                                <td>{{ $void->address ?? 'N/A' }}</td>
                                <td>{{ $void->property_type ?? 'N/A' }}</td>
                                <td>{{ $void->property_subtype ?? 'N/A' }}</td>
                                <td>{{ $void->bedrooms ?? 'N/A' }}</td>
                                <td>{{ $void->void_status ?? '-' }}</td>
                                <td>{{ $void->hfi_code ?? '-' }}</td>
                                <td>{{ $void->uprn ?? '-' }}</td>
                                <td>{{ $void->ten_reason ?? '-' }}</td>
                                <td>{{ $void->vin_sco_code ?? '-' }}</td>
                                <td>{{ $void->updates ?? '-' }}</td>
                                <td>{{ $void->previous_call_over ?? '-' }}</td>
                                <td>{{ $void->void_ref ?? '-' }}</td>
                                <td>{{ $void->termination_date ?? '-' }}</td>
                                <td>{{ $void->ready_for_let_date ?? '-' }}</td>
                                <td>{{ $void->management_unit ?? '-' }}</td>
                                <td>
                                    @if($void->termination_date)
                                        {{ Carbon::parse($void->termination_date)->diffInDays(Carbon::now()) }}
                                    @else
                                        N/A
                                    @endif
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
