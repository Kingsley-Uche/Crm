@extends('layouts.dashboard.landpage')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row px-3 py-3">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">FOB Manager</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('occupant.index') }}">FOB Manager</a></li>
                                <li class="breadcrumb-item active">Assign FOB</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <h4 class="card-title">Assign New FOB</h4>
                <p class="card-title-desc">Please provide the necessary details to assign a FOB to a tenant.</p>

                <form method="POST" action="{{ route('fobs.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tenant_id">Tenant</label>
                            <select name="tenant_id" class="form-control" required>
                                <option value="">-- Select Tenant --</option>
                                @foreach($tenants as $tenant)
                                    <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                        {{ $tenant->full_name  }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tenant_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="fob_uid">FOB UID</label>
                            <input type="text" name="fob_uid" class="form-control" required placeholder="Enter unique FOB UID" value="{{ old('fob_uid') }}">
                            @error('fob_uid') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="make">Make</label>
                            <input type="text" name="make" class="form-control" placeholder="FOB Manufacturer" value="{{ old('make') }}">
                            @error('make') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="model">Model</label>
                            <input type="text" name="model" class="form-control" placeholder="FOB Model" value="{{ old('model') }}">
                            @error('model') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="type">Type</label>
                            <select name="type" class="form-control" required>
                                <option value="rfid" {{ old('type') == 'rfid' ? 'selected' : '' }}>RFID</option>
                                <option value="nfc" {{ old('type') == 'nfc' ? 'selected' : '' }}>NFC</option>
                                <option value="ble" {{ old('type') == 'ble' ? 'selected' : '' }}>BLE</option>
                                <option value="smartcard" {{ old('type') == 'smartcard' ? 'selected' : '' }}>Smartcard</option>
                            </select>
                            @error('type') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="fob_status">Status</label>
                            <select name="fob_status" class="form-control" required>
                                <option value="active" {{ old('fob_status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="deactivated" {{ old('fob_status') == 'deactivated' ? 'selected' : '' }}>Deactivated</option>
                            </select>
                            @error('fob_status') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="request_date">Request Date</label>
                            <input type="datetime-local" name="request_date" class="form-control" value="{{ old('request_date') }}">
                            @error('request_date') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="issued_date">Issued Date</label>
                            <input type="datetime-local" name="issued_date" class="form-control" value="{{ old('issued_date') }}">
                            @error('issued_date') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="fee">Fee</label>
                            <input type="number" name="fee" class="form-control" step="0.01" placeholder="0.00" value="{{ old('fee') }}">
                            @error('fee') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="request_reason">Request Reason</label>
                            <textarea name="request_reason" class="form-control" rows="3">{{ old('request_reason') }}</textarea>
                            @error('request_reason') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="mb-0">
                        <button type="submit" class="btn btn-success waves-effect waves-light me-1">
                            Submit
                        </button>
                        <a href="{{ route('fobs.index') }}" class="btn btn-secondary waves-effect">Cancel</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
