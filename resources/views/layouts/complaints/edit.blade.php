@extends('layouts.dashboard.landpage')

@section('content')
    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 px-1">Edit Complaint</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('complaints.index') }}">Complaints</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm rounded-4 mb-5 p-3">
                <div class="card-body p-4">
                    <h6 class="text-center text-muted mb-4">Edit Complaint Form</h6>

                    <form method="POST" action="{{ route('complaints.update', $complaint->id) }}" id="complaintForm">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6 col-xl-4">
                                <label>Block Name <span class="text-danger">*</span></label>
                                <select name="block_id" id="block_id" class="form-select select2 @error('block_id') is-invalid @enderror" required>
                                    <option value="" disabled>Select a block</option>
                                    @foreach(json_decode($blocks) as $block)
                                        <option value="{{ $block->id }}" {{ old('block_id', $complaint->block_id) == $block->id ? 'selected' : '' }}>
                                            {{ ucwords($block->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('block_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <label>Apartment <span class="text-danger">*</span></label>
                                <select name="apartment_id" id="apartment_id" class="form-select select2 @error('apartment_id') is-invalid @enderror" required>
                                    <option value="" disabled selected>Select an apartment</option>
                                </select>
                                @error('apartment_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <label>Unit Number <span class="text-danger">*</span></label>
                                <input type="text" name="unit_number" id="unit_number" class="form-control @error('unit_number') is-invalid @enderror"
                                       value="{{ old('unit_number', $complaint->unit_number) }}" required readonly>
                                @error('unit_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            @php $fieldCount = 0; @endphp
                            @foreach ($fields as $field)
                                @if ($field['name'] !== 'description')
                                    @if ($fieldCount % 3 == 0)
                                        </div><div class="row g-3 mt-2">
                                    @endif
                                    <div class="col-md-6 col-xl-4">
                                        <label>{{ $field['label'] }} @if(!empty($field['required'])) <span class="text-danger">*</span> @endif</label>
                                        @if ($field['type'] === 'select')
                                            <select name="{{ $field['name'] }}"
                                                    class="form-select {{ $field['name'] === 'status' ? 'no-select2' : 'select2' }} @error($field['name']) is-invalid @enderror"
                                                    style="{{ $field['name'] === 'status' ? 'max-width: 100%; width: 100%;' : '' }}"
                                                    @if(!empty($field['required'])) required @endif>
                                                <option value="" disabled>Select</option>
                                                @foreach ($field['options'] as $option)
                                                    <option value="{{ is_array($option) ? $option['value'] : $option }}"
                                                            {{ old($field['name'], $complaint->{$field['name']}) == (is_array($option) ? $option['value'] : $option) ? 'selected' : '' }}>
                                                        {{ is_array($option) ? $option['label'] : $option }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @elseif ($field['type'] === 'textarea')
                                            <textarea name="{{ $field['name'] }}"
                                                      class="form-control @error($field['name']) is-invalid @enderror"
                                                      rows="4"
                                                      placeholder="{{ $field['label'] }}"
                                                      @if(!empty($field['required'])) required @endif>{{ old($field['name'], $complaint->{$field['name']}) }}</textarea>
                                        @else
                                            <input type="{{ $field['type'] }}"
                                                   name="{{ $field['name'] }}"
                                                   value="{{ old($field['name'], $complaint->{$field['name']}) }}"
                                                   class="form-control @error($field['name']) is-invalid @enderror"
                                                   placeholder="{{ $field['label'] }}"
                                                   @if(!empty($field['required'])) required @endif>
                                        @endif
                                        @error($field['name']) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    @php $fieldCount++; @endphp
                                @endif
                            @endforeach
                            @while ($fieldCount % 3 != 0)
                                <div class="col-md-6 col-xl-4"></div>
                                @php $fieldCount++; @endphp
                            @endwhile
                            </div>

                            <!-- Description -->
                            @foreach ($fields as $field)
                                @if ($field['name'] === 'description')
                                    <div class="row g-3 mt-2">
                                        <div class="col-12">
                                            <label>{{ $field['label'] }} @if(!empty($field['required'])) <span class="text-danger">*</span> @endif</label>
                                            <textarea name="{{ $field['name'] }}"
                                                      class="form-control @error($field['name']) is-invalid @enderror"
                                                      rows="4"
                                                      placeholder="{{ $field['label'] }}"
                                                      @if(!empty($field['required'])) required @endif>{{ old($field['name'], $complaint->{$field['name']}) }}</textarea>
                                            @error($field['name']) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            <!-- Submit and Cancel Buttons -->
                            <div class="row g-3 mt-3">
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-success rounded">Update Complaint</button>
                                    <a href="{{ route('complaints.index') }}" class="btn btn-secondary rounded">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .no-select2, .no-select2 ~ .select2-container {
        float: none !important;
        width: 100% !important;
        max-width: 100% !important;
        text-align: left !important;
    }
    .form-select.no-select2 {
        display: block !important;
    }
</style>
@endsection

@section('scripts')
<script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
<script>
    const blocks = @json(json_decode($blocks));
    const blockSelect = document.getElementById('block_id');
    const apartmentSelect = document.getElementById('apartment_id');
    const unitNumberInput = document.getElementById('unit_number');

    function clearApartments() {
        apartmentSelect.innerHTML = '<option value="" disabled selected>Select an apartment</option>';
        unitNumberInput.value = '';
    }

    function populateApartments(selectedBlockId, selectedApartmentId) {
        clearApartments();
        const block = blocks.find(b => b.id === parseInt(selectedBlockId));

        if (block?.apartments) {
            block.apartments.forEach(apartment => {
                const option = document.createElement('option');
                option.value = apartment.id;
                const addr = apartment.address?.trim() || `Flat ${apartment.unit_number || apartment.id}`;
                option.text = addr.replace(/\w\S*/g, w => w.charAt(0).toUpperCase() + w.slice(1));
                if (apartment.id === parseInt(selectedApartmentId)) {
                    option.selected = true;
                }
                apartmentSelect.appendChild(option);
            });

            const apt = block.apartments.find(a => a.id === parseInt(selectedApartmentId));
            unitNumberInput.value = apt?.unit_number || '';
        }
    }

    blockSelect.addEventListener('change', function () {
        populateApartments(this.value, '');
        $(apartmentSelect).trigger('change');
    });

    apartmentSelect.addEventListener('change', function () {
        const block = blocks.find(b => b.id === parseInt(blockSelect.value));
        const apt = block?.apartments?.find(a => a.id === parseInt(this.value));
        unitNumberInput.value = apt?.unit_number || '';
    });

    $(document).ready(function () {
        if (blockSelect.value) {
            populateApartments(blockSelect.value, {{ old('apartment_id', $complaint->apartment_id) }});
            $(apartmentSelect).trigger('change');
        }
    });
</script>
@endsection
