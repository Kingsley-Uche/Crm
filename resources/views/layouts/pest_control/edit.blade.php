
@extends('layouts.dashboard.landpage')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <style>
        /* Ensure native select is hidden to prevent duplication */
        .select2-hidden-accessible {
            display: none !important;
        }
        /* Fix potential Select2 container positioning */
        .select2-container {
            width: 100% !important;
        }
        .select2-selection__rendered {
            line-height: normal !important;
        }
    </style>
@endsection

@section('content')
@if (session('success'))
    <div class="alert alert-success mb-4">
        {{ session('success') }}
    </div>
@endif

<!-- Display general validation errors -->
@if ($errors->any())
    <div class="alert alert-danger mb-4">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row mb-4">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 px-1">Pest Control Reports</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('pest_control.index') }}">Pest Reports</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm rounded-4 mb-5">
            <div class="card-body p-4">
                <h6 class="text-center text-muted mb-4">Edit Pest Control Report</h6>

                <form action="{{ route('pest_control.update', ['pest_id' => $pest->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <!-- Block Selection -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Block Name <span class="text-danger">*</span></label>
                            <select name="block_id" id="block_id" class="form-select select2 @error('block_id') is-invalid @enderror" required>
                                <option value="" disabled>Select a block</option>
                                @foreach(json_decode($blocks) as $block)
                                    <option value="{{ $block->id }}" {{ old('block_id', $pest->block_id) == $block->id ? 'selected' : '' }}>
                                        {{ ucwords($block->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('block_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Apartment Selection -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Apartment <span class="text-danger">*</span></label>
                            <select name="apartment_id" id="apartment_id" class="form-select select2 @error('apartment_id') is-invalid @enderror" required>
                                <option value="" disabled>Select an apartment</option>
                                <!-- Populated via JavaScript -->
                            </select>
                            @error('apartment_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Unit Number -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Unit Number <span class="text-danger">*</span></label>
                            <input type="text" name="unit_number" id="unit_number" class="form-control @error('unit_number') is-invalid @enderror"
                                   value="{{ old('unit_number', $pest->apartment->unit_number ?? '') }}" placeholder="Enter unit number" readonly required>
                            @error('unit_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                         <div class="col-md-6 col-xl-4 mb-3">
                            <label>Ref <span class="text-danger">*</span></label>
                            <input type="text" name="ref" id="ref" class="form-control @error('ref') is-invalid @enderror"
                                   value="{{ old('ref', $pest->ref ?? '') }}" placeholder="Referal Number" readonly required>
                            @error('ref') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <!-- Dynamic Fields -->
                        @foreach ($fields as $field)
                            <div class="col-md-6 col-xl-4 mb-3">
                                <label>
                                    {{ $field['label'] }}
                                    @if(in_array($field['name'], ['issue_type', 'received_date', 'progress', 'deadline_timeframe', 'appointment_timeframe', 'action_timeline', 'assigned_to', 'due_date', 'pest_control_fee']))
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>

                                @if ($field['type'] === 'select')
                                    <select name="{{ $field['name'] }}" class="form-select @error($field['name']) is-invalid @enderror" {{ $field['name'] == 'progress' ? 'required' : '' }}>
                                        <option value="" disabled>Select</option>
                                        @foreach ($field['options'] as $option)
                                            <option value="{{ $option }}" {{ old($field['name'], $pest->{$field['name']}) == $option ? 'selected' : '' }}>
                                                {{ $option }}
                                            </option>
                                        @endforeach
                                    </select>
                                @elseif ($field['type'] === 'textarea')
                                    <textarea name="{{ $field['name'] }}" class="form-control @error($field['name']) is-invalid @enderror" rows="4"
                                              placeholder="{{ $field['label'] }}" {{ $field['name'] === 'description' ? 'required' : '' }}>{{ old($field['name'], $pest->{$field['name']}) }}</textarea>
                                @else
                                    <input type="{{ $field['type'] }}" name="{{ $field['name'] }}"
                                           value="{{ old($field['name'], $pest->{$field['name']}) }}"
                                           class="form-control @error($field['name']) is-invalid @enderror"
                                           placeholder="{{ $field['label'] }}"
                                           {{ in_array($field['name'], ['issue_type', 'received_date', 'deadline_timeframe', 'appointment_timeframe', 'action_timeline', 'assigned_to', 'due_date', 'pest_control_fee']) ? 'required' : '' }}>
                                @endif
                                @error($field['name']) <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        @endforeach

                        <!-- Image Upload -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Upload New Image</label>
                            @if ($pest->image)
                                <div class="mb-2">
                                    <a href="{{ asset('storage/' . $pest->image) }}" target="_blank">View Current Image</a>
                                </div>
                            @endif
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png">
                            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12 text-end mt-4">
                            <button type="submit" class="btn btn-success rounded">Update Pest Report</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize Select2 only once
            $('.select2').select2();

            const blocks = @json(json_decode($blocks));
            const blockSelect = document.getElementById('block_id');
            const apartmentSelect = document.getElementById('apartment_id');
            const unitNumberInput = document.getElementById('unit_number');
            const currentApartmentId = '{{ old('apartment_id', $pest->apartment_id) }}';

            function clearApartments() {
                apartmentSelect.innerHTML = '<option value="" disabled>Select an apartment</option>';
                $(apartmentSelect).select2('destroy').select2();
            }

            function populateApartments(blockId) {
                clearApartments();
                const block = blocks.find(b => b.id === parseInt(blockId));
                if (!block || !block.apartments) {
                    console.warn('No apartments found for block ID:', blockId);
                    return;
                }

                block.apartments.forEach(apartment => {
                    const addr = apartment.address || '';
                    const displayText = addr.trim() || `Apartment ${apartment.id}`;
                    const option = new Option(
                        displayText.replace(/\w\S*/g, (w) => w.charAt(0).toUpperCase() + w.slice(1)),
                        apartment.id
                    );
                    apartmentSelect.appendChild(option);
                    if (apartment.id == currentApartmentId) {
                        option.selected = true;
                    }
                });

                if (currentApartmentId) {
                    apartmentSelect.value = currentApartmentId;
                    updateUnitNumber(currentApartmentId);
                }

                // Log options for debugging
                console.log('Apartment dropdown options:', apartmentSelect.innerHTML);

                // Reinitialize Select2
                $(apartmentSelect).select2('destroy').select2();
            }

            function updateUnitNumber(apartmentId) {
                const block = blocks.find(b => b.id === parseInt(blockSelect.value));
                const apartment = block?.apartments?.find(a => a.id === parseInt(apartmentId));
                unitNumberInput.value = apartment?.unit_number || '';
            }

            blockSelect.addEventListener('change', () => {
                const blockId = blockSelect.value;
                if (blockId) {
                    populateApartments(blockId);
                } else {
                    clearApartments();
                    unitNumberInput.value = '';
                }
            });

            apartmentSelect.addEventListener('change', () => {
                const apartmentId = apartmentSelect.value;
                if (apartmentId) {
                    updateUnitNumber(apartmentId);
                } else {
                    unitNumberInput.value = '';
                }
            });

            // Initialize apartments for the current block
            if (blockSelect.value) {
                populateApartments(blockSelect.value);
            } else {
                console.warn('No block selected initially');
            }
        });
    </script>
@endsection
