@extends('layouts.dashboard.landpage')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Parking Data</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('locations.index') }}">Parking</a></li> 
                    <li class="breadcrumb-item active">Status</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <form id="parkingForm">
            @csrf

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Get Parking Status</h4>
                    <p class="card-title-desc">Enter the dates to get parking details.</p>

                    <div class="row mb-3">
                        <label for="start_date" class="col-sm-2 col-form-label">Start Date</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="date" id="start_date" name="start_date" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="end_date" class="col-sm-2 col-form-label">End Date</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="date" id="end_date" name="end_date" required>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">Get Details</button>
                        <a href="#" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="col-12" id="parkingTableWrapper">
        <!-- Loaded data will show here -->
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('parkingForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        fetch('{{ route('parking.status') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ start_date: startDate, end_date: endDate })
        })
        .then(response => response.text())
        .then(html => {
            document.getElementById('parkingTableWrapper').innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('parkingTableWrapper').innerHTML = `<div class="alert alert-danger">Failed to load data.</div>`;
        });
    });
</script>
@endsection
