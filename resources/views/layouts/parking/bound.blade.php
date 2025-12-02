@extends('layouts.dashboard.landpage')

<style>
    .nav-pills .nav-link {
        background-color: white;
        color: #000;
    }

    .nav-pills .nav-link.active {
        background-color: #198754 !important; /* Bootstrap success color */
        color: #fff !important;
    }
</style>

@section('content')
<div class="row">
    <div class="col-12">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="row px-3 py-3">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0">Parking Capture</h4>
                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('park.taxes.index') }}">Parking</a>
                                    </li>
                                    <li class="breadcrumb-item active">Capture</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title mb-4"><i class="fas fa-parking me-2"></i>Parking Capture</h4>

                            <!-- Nav tabs -->
                            <ul class="nav nav-pills nav-justified mb-3" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active text-black" data-bs-toggle="tab" href="#inbound" role="tab">
                                        <i class="fas fa-arrow-right me-1"></i>Inbound
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-dark" data-bs-toggle="tab" href="#outbound" role="tab">
                                        <i class="fas fa-arrow-left me-1"></i>Outbound
                                    </a>
                                </li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content text-muted">
                                <!-- Inbound Form -->
                                <div class="tab-pane fade show active" id="inbound" role="tabpanel">
                                    <form id="inboundForm">
                                        <div class="mb-3">
                                            <label for="inboundPasscode" class="form-label">Enter Passcode</label>
                                            <input type="password" class="form-control" id="inboundPasscode" name="passcode" placeholder="******" required>
                                        </div>
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-check-circle me-1"></i>Submit Inbound
                                        </button>
                                    </form>
                                </div>

                                <!-- Outbound Form -->
                                <div class="tab-pane fade" id="outbound" role="tabpanel">
                                    <form id="outboundForm">
                                        <div class="mb-3">
                                            <label for="outboundPasscode" class="form-label">Enter Passcode</label>
                                            <input type="password" class="form-control" id="outboundPasscode" name="passcode" placeholder="******" required>
                                        </div>
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-check-circle me-1"></i>Submit Outbound
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div> <!-- end card-body -->
                    </div> <!-- end card -->
                </div> <!-- end col-xl-6 -->
            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div> <!-- end col-12 -->
</div> <!-- end row -->
@endsection

@section('scripts')

<script>
    // Inbound submission
    document.getElementById('inboundForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const passcode = document.getElementById('inboundPasscode').value;

        fetch('{{ route("parking.inbound") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ passcode })
        })
        .then(response => response.json())
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: data.message || 'Inbound submitted successfully',
                timer: 2500,
                showConfirmButton: false
            });
            document.getElementById('inboundForm').reset();
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Something went wrong'
            });
        });
    });

    // Outbound submission
    document.getElementById('outboundForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const passcode = document.getElementById('outboundPasscode').value;

        fetch('{{ route("parking.outbound") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ passcode })
        })
        .then(response => response.json())
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: data.message || 'Outbound submitted successfully',
                timer: 2500,
                showConfirmButton: false
            });
            document.getElementById('outboundForm').reset();
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Something went wrong'
            });
        });
    });
</script>
@endsection
