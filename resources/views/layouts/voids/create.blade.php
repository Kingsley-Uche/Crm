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
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Create Void</h4>

                    <form action="{{ route('void.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            @php
                                $fields = [
                                    'void_path' => 'Void Path',
                                    'void_classification' => 'Void Classification',
                                    'hfi_code' => 'HFI Code',
                                    'uprn' => 'UPRN',
                                    'property_ref' => 'Property Ref',
                                    'ten_reason' => 'Ten Reason',
                                    'address' => 'Address',
                                    'property_type' => 'Property Type',
                                    'property_subtype' => 'Property Subtype',
                                    'bedrooms' => 'Bedrooms',
                                    'void_status' => 'Void Status',
                                    'vin_sco_code' => 'VIN SCO Code',
                                    'termination_date' => 'Termination Date',
                                    'ready_for_let_date' => 'Ready for Let Date',
                                    'management_unit' => 'Management Unit',
                                ];
                            @endphp

                            @foreach($fields as $field => $label)
                                <div class="col-md-{{ in_array($field, ['address', 'termination_date', 'ready_for_let_date', 'management_unit']) ? 8 : 4 }}">
                                    <label for="{{ $field }}" class="form-label">{{ $label }}</label>
                                    <input
                                        type="{{ in_array($field, ['termination_date', 'ready_for_let_date']) ? 'date' : ($field === 'bedrooms' ? 'number' : 'text') }}"
                                        name="{{ $field }}"
                                        id="{{ $field }}"
                                        class="form-control"
                                        value="{{ old($field) }}"
                                        @if($field === 'bedrooms') min="0" @endif
                                    >
                                    @error($field)
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endforeach

                            <div class="col-md-6">
                                <label for="updates" class="form-label">Updates</label>
                                <textarea name="updates" id="updates" class="form-control" rows="3">{{ old('updates') }}</textarea>
                                @error('updates')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="previous_call_over" class="form-label">Previous Call Over</label>
                                <textarea name="previous_call_over" id="previous_call_over" class="form-control" rows="3">{{ old('previous_call_over') }}</textarea>
                                @error('previous_call_over')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-success w-100">Create Void</button>
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
        $(document).ready(function () {
            $('#termination_date, #ready_for_let_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
            });
        });
    </script>
@endsection
