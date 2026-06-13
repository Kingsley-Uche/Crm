@extends('layouts.dashboard.landpage')

@section('content')

<div class="container-fluid">

    {{-- ================= HEADER ================= --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Executive Dashboard</h4>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item active">Analytics</li>
                </ol>
            </div>
        </div>
    </div>


    {{-- ================= KPI CARDS ================= --}}
    <div class="row g-3">

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Incomplete Repairs</p>
                    <h3 class="text-danger">{{ $total_uncompleted }}</h3>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Voids</p>
                    <h3 class="text-warning">{{ $voids->sum() }}</h3>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Active Tenants</p>
                    <h3 class="text-success">{{ $tenants }}</h3>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Locations</p>
                    <h3 class="text-primary">{{ $locations }}</h3>
                </div>
            </div>
        </div>

    </div>


    {{-- ================= SECOND ROW ================= --}}
    <div class="row mt-3 g-3">

        {{-- Shelter Types --}}
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    <h6 class="mb-3">Shelter Portfolio Distribution</h6>

                    <ul class="list-group list-group-flush">
                        @foreach ($shelterTypeCounts as $item)
                            <li class="list-group-item d-flex justify-content-between">
                                <span>{{ ucfirst($item->shelter_name) }}s</span>
                                <strong>{{ $item->total }}</strong>
                            </li>
                        @endforeach
                    </ul>

                </div>
            </div>
        </div>


        {{-- Repairs --}}
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    <h6 class="mb-3">Repair Lifecycle Status</h6>

                    <ul class="list-group list-group-flush">
                        @foreach($repairProgressCounts as $status => $count)
                            <li class="list-group-item d-flex justify-content-between">
                                <span>{{ ucfirst($status) }}</span>
                                <strong>{{ $count }}</strong>
                            </li>
                        @endforeach
                    </ul>

                </div>
            </div>
        </div>


        {{-- ================= OCCUPANCY RATE (FIXED PIE CHART) ================= --}}
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    <h6 class="mb-3">Occupancy Rate by Shelter</h6>

                    <div id="occupancy_chart"></div>

                </div>
            </div>
        </div>

    </div>


    {{-- ================= BOOKINGS CHART ================= --}}
    <div class="row mt-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    <h6 class="mb-3">Bookings Overview (Annual Trend)</h6>
                    <div id="area_chart"></div>

                </div>
            </div>
        </div>
    </div>

</div>

@endsection


{{-- ================= SCRIPTS ================= --}}
@section('scripts')

<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

<script>

/* ================= BOOKINGS (AREA CHART) ================= */
var bookingOptions = {
    chart: {
        type: 'area',
        height: 350,
        toolbar: { show: false }
    },
    series: [{
        name: 'Bookings',
        data: @json($booked['data'])
    }],
    xaxis: {
        categories: @json($booked['labels'])
    },
    stroke: { curve: 'smooth' },
    dataLabels: { enabled: false }
};

new ApexCharts(document.querySelector("#area_chart"), bookingOptions).render();


/* ================= OCCUPANCY RATE (FIXED) ================= */
var occupancyOptions = {
    chart: {
        type: 'pie',
        height: 320
    },
    labels: @json($occupancyByShelter->pluck('shelter_name')),
    series: @json($occupancyByShelter->pluck('occupancy_percent')),
    legend: {
        position: 'bottom'
    }
};

new ApexCharts(document.querySelector("#occupancy_chart"), occupancyOptions).render();

</script>

@endsection