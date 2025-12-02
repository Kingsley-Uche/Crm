
@extends('layouts.dashboard.landpage')
    <script src="assets/libs/apexcharts/apexcharts.min.js"></script
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
        @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
</div>
    </div>
</div>

  <!-- start page title -->
  <div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Dashboard</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                    <li class="breadcrumb-item active">Analytics</li>
                </ol>
            </div>
        </div>
    </div>
</div>


<div class="row">
                            <div class="col-xl-3 col-md-6">
                              <div class="card">
    <div class="card-body">
        <div class="d-flex mb-3">
            <div class="flex-grow-1">
                <p class="text-truncate font-size-14 mb-2">Total Blocks</p>
                <h4 class="mb-0 text-success">{{ $totalBlocks }}</h4>
            </div>
            <div class="avatar-sm">
    <span class="avatar-title bg-light text-success rounded-3">
        <i class="ri-home-4-line font-size-24 text-black"></i>
    </span>
</div>

        </div>

        @if(!empty($shelterTypeCounts))
            <hr>
            <p class="text-truncate font-size-14 mb-2 text-center"><b>Shelter Types</b></p>
            <ul class="list-unstyled mb-0">
                @foreach ($shelterTypeCounts as $type => $count)
                    <li class="d-flex justify-content-between py-1">
                        <span>{{ ucfirst($type) }}</span>
                        <span class="fw-bold ">{{ $count }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-muted mt-3">No shelter types available.</p>
        @endif
    </div>
</div>

                            </div><!-- end col -->
                            <div class="col-xl-3 col-md-6">
    <div class="card">
    <div class="card-body">
        <div class="d-flex mb-3">
            <div class="flex-grow-1">
                <p class="text-truncate font-size-14 mb-2">Total Incomplete Repairs</p>
                <h4 class="mb-0 text-danger">
                    {{ 
                        (int)($total_uncompleted)
                    }}
                </h4>
            </div>
          <div class="avatar-sm">
                <span class="avatar-title bg-light text-primary rounded-3">
                    <i class="ri-tools-line font-size-24"></i>  
                </span>
            </div>
        </div>

        @if(!empty($repairProgressCounts))
                     <hr>
            <p class="text-truncate font-size-14 mb-2 text-center"><b>Repair Stage</b></p>
            <ul class="list-unstyled mb-0">
                @foreach($repairProgressCounts as $status => $count)
                    <li class="d-flex justify-content-between py-1">
                        <span>{{ $status ?: 'Unknown' }}</span>
                        <span class="text-muted fw-bold">{{ $count }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-muted mt-3">No repair data available.</p>
        @endif
    </div><!-- end card-body -->
</div><!-- end card -->




                            </div><!-- end col -->
                            <div class="col-xl-3 col-md-6">
                                <div class="card">
    <div class="card-body">
        <div class="d-flex mb-3">
            <div class="flex-grow-1">
                <p class="text-truncate font-size-14 mb-2">Total Voids</p>
                <h4 class="mb-0 text-warning">{{ $voids->sum() }}</h4>
            </div>
            <div class="avatar-sm">
                <span class="avatar-title bg-light text-danger rounded-3">
                    <i class="ri-indeterminate-circle-line font-size-24 text-black"></i>

                </span>
            </div>
        </div>

        @if($voids->isNotEmpty())
            <hr>
            <p class="text-truncate font-size-14 mb-2 text-center"><strong>Property Type</strong></p>
            <ul class="list-unstyled mb-0">
                @foreach ($voids as $type => $count)
                    <li class="d-flex justify-content-between py-1">
                        <span>{{ ucfirst($type) }}</span>
                        <span class="fw-bold">{{ $count }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-muted mt-3">No voids recorded.</p>
        @endif
    </div>
</div>

                            </div><!-- end col -->
                            <div class="col-xl-3 col-md-6">
                              <div class="card">
    <div class="card-body">
        <div class="d-flex">
            <div class="flex-grow-1">
                <p class="text-truncate font-size-14 mb-2">Tenants</p>
                <h4 class="mb-2">{{ $tenants }}</h4>
            </div>
            <div class="avatar-sm">
                <span class="avatar-title bg-light text-success rounded-3">
                    <i class="ri-user-line font-size-24 text-black"></i>  
                </span>
            </div>
        </div>                                              
    </div><!-- end cardbody -->
</div><!-- end card -->

                            </div><!-- end col -->
                        </div><!-- end row -->

<div class='row'>
    
    <div class='col-12 mb-2'>
        <div class="card-body py-0 px-2">
            <h6 class='title'>Bookings</h6>
            <div id="area_chart" class="apex-charts bg-white" dir="ltr"></div>
        </div>
    </div>
</div>
                        

@endsection
@section('scripts')
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
    var options = {
        chart: {
            type: 'area',
            height: 350,
        },
        series: [{
            name: 'Bookings',
            data: @json($booked['data'])
        }],
        xaxis: {
            categories: @json($booked['labels']),
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth'
        },
        tooltip: {
            x: {
                format: 'MMM'
            },
        },
    };

    var chart = new ApexCharts(document.querySelector("#area_chart"), options);
    chart.render();
</script>
@endsection
