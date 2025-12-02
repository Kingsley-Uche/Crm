@extends('layouts.dashboard.landpage')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Maintenace Report</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('locations.index') }}">Maintenance</a></li> 
                    <li class="breadcrumb-item active">Report</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <form action="{{ route('maintenance.report.generate') }}" method="POST">
            @csrf

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Generate Maintenace Report</h4>
                    <p class="card-title-desc">Enter the dates to generate maintenace report.</p>

                    <div class="row mb-3">
                        <label for="start_date" class="col-sm-2 col-form-label">Start Date</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="date" id="start_date" name="start_date" placeholder="Enter start date" value="{{ old('name') }}">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="end_date" class="col-sm-2 col-form-label">End Date</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="date" id="end_date" name="end_date" placeholder="Enter end date" value="{{ old('name') }}">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">Create</button>
                        <a href="{{ route('rent.report') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
