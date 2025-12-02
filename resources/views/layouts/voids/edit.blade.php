@extends('layouts.dashboard.landpage')

@section('styles')
    <link href="{{ asset('assets/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 px-1">Void</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('void.index') }}">Voids</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Void</h4>

                    <form action="{{ route('voids.update', $void->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="void_path" class="form-label">Void Path</label>
                                <input type="text" name="void_path" id="void_path" class="form-control" value="{{ old('void_path', $void->void_path) }}">
                                @error('void_path')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="void_classification" class="form-label">Void Classification</label>
                                <input type="text" name="void_classification" id="void_classification" class="form-control" value="{{ old('void_classification', $void->void_classification) }}">
                                @error('void_classification')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="hfi_code" class="form-label">HFI Code</label>
                                <input type="text" name="hfi_code" id="hfi_code" class="form-control" value="{{ old('hfi_code', $void->hfi_code) }}">
                                @error('hfi_code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="uprn" class="form-label">UPRN</label>
                                <input type="text" name="uprn" id="uprn" class="form-control" value="{{ old('uprn', $void->uprn) }}">
                                @error('uprn')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="property_ref" class="form-label">Property Ref</label>
                                <input type="text" name="property_ref" id="property_ref" class="form-control" value="{{ old('property_ref', $void->property_ref) }}">
                                @error('property_ref')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="ten_reason" class="form-label">Ten Reason</label>
                                <input type="text" name="ten_reason" id="ten_reason" class="form-control" value="{{ old('ten_reason', $void->ten_reason) }}">
                                @error('ten_reason')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="void_ref" class="form-label">Void Ref</label>
                                <input type="text" name="void_ref" id="void_ref" class="form-control" value="{{ old('void_ref', $void->void_ref) }}">
                                @error('void_ref')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" name="address" id="address" class="form-control" value="{{ old('address', $void->address) }}">
                                @error('address')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="updates" class="form-label">Updates</label>
                                <textarea name="updates" id="updates" class="form-control" rows="3">{{ old('updates', $void->updates) }}</textarea>
                                @error('updates')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="previous_call_over" class="form-label">Previous Call Over</label>
                                <textarea name="previous_call_over" id="previous_call_over" class="form-control" rows="3">{{ old('previous_call_over', $void->previous_call_over) }}</textarea>
                                @error('previous_call_over')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="property_type" class="form-label">Property Type</label>
                                <input type="text" name="property_type" id="property_type" class="form-control" value="{{ old('property_type', $void->property_type) }}">
                                @error('property_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="property_subtype" class="form-label">Property Subtype</label>
                                <input type="text" name="property_subtype" id="property_subtype" class="form-control" value="{{ old('property_subtype', $void->property_subtype) }}">
                                @error('property_subtype')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="bedrooms" class="form-label">Bedrooms</label>
                                <input type="number" name="bedrooms" id="bedrooms" class="form-control" value="{{ old('bedrooms', $void->bedrooms) }}" min="0">
                                @error('bedrooms')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="void_status" class="form-label">Void Status</label>
                                <input type="text" name="void_status" id="void_status" class="form-control" value="{{ old('void_status', $void->void_status) }}">
                                @error('void_status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="vin_sco_code" class="form-label">VIN SCO Code</label>
                                <input type="text" name="vin_sco_code" id="vin_sco_code" class="form-control" value="{{ old('vin_sco_code', $void->vin_sco_code) }}">
                                @error('vin_sco_code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="days_void" class="form-label">Days Void</label>
                                <input type="number" name="days_void" id="days_void" class="form-control" value="{{ old('days_void', $void->days_void) }}" min="0">
                                @error('days_void')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="termination_date" class="form-label">Termination Date</label>
                                <input type="date" name="termination_date" id="termination_date" class="form-control" value="{{ old('termination_date', $void->termination_date ? $void->termination_date->format('Y-m-d') : '') }}">
                                @error('termination_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="ready_for_let_date" class="form-label">Ready for Let Date</label>
                                <input type="date" name="ready_for_let_date" id="ready_for_let_date" class="form-control" value="{{ old('ready_for_let_date', $void->ready_for_let_date ? $void->ready_for_let_date->format('Y-m-d') : '') }}">
                                @error('ready_for_let_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="management_unit" class="form-label">Management Unit</label>
                                <input type="text" name="management_unit" id="management_unit" class="form-control" value="{{ old('management_unit', $void->management_unit) }}">
                                @error('management_unit')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-success w-100">Update Void</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#termination_date, #ready_for_let_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
            });
        });
    </script>
@endsection