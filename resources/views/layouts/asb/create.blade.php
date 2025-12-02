@extends('layouts.dashboard.landpage')

@section('content')

@if (session('success'))
    <div class="alert alert-success mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="row mb-4">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 px-1">Anti-Social Behaviours[ASB]</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('asb.index') }}">ASB</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm rounded-4 mb-5">
            <div class="card-body p-4">
                <h6 class='text-center text-muted mb-4'>Anti-Social Behaviours[ASB]</h6>

                <form action="{{ route('asb.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-4">
                        <!-- Block Selection -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Block Name <span class="text-danger">*</span></label>
                            <select name="block_id" id="block_id" class="form-select select2 @error('block_id') is-invalid @enderror" required>
                                <option value="" disabled selected>Select a block</option>
                                @foreach(json_decode($blocks) as $block)
                                    <option value="{{ $block->id }}" {{ old('block_id') == $block->id ? 'selected' : '' }}>
                                        {{ ucwords($block->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('block_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Apartment <span class="text-danger">*</span></label>
                            <select name="apartment_id" id="apartment_id" class="form-select select2 @error('apartment_id') is-invalid @enderror" required>
                                <option value="" disabled selected>Select an apartment</option>
                            </select>
                            @error('apartment_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Unit Number <span class="text-danger">*</span></label>
                            <input type="text" name="unit_number" id="unit_number" class="form-control @error('unit_number') is-invalid @enderror"
                                   value="{{ old('unit_number') }}" placeholder="Enter unit number" readonly required>
                            @error('unit_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Dynamic Fields -->
                        @foreach ($fields as $field)
                            <div class="col-md-6 col-xl-4 mb-3">
                                <label>{{ $field['label'] }} @if(!in_array($field['name'], ['status', 'appointment', 'completion_date']))<span class="text-danger">*</span>@endif</label>

                                @if ($field['type'] === 'select')
                                    <select name="{{ $field['name'] }}" class="form-control @error($field['name']) is-invalid @enderror" required>
                                        <option value="" disabled selected>Select</option>
                                        @foreach ($field['options'] as $option)
                                            <option value="{{ $option }}" {{ old($field['name']) == $option ? 'selected' : '' }}>{{ $option }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="{{ $field['type'] }}" name="{{ $field['name'] }}"
                                           value="{{ old($field['name']) }}"
                                           class="form-control @error($field['name']) is-invalid @enderror"
                                           placeholder="{{ $field['label'] }}">
                                @endif
                                @error($field['name']) <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        @endforeach

                        <!-- Reporter Email -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Reporter Email <span class="text-danger">*</span></label>
                            <input type="email" name="reporter_email" class="form-control @error('reporter_email') is-invalid @enderror" value="{{ old('reporter_email') }}" placeholder="Enter reporter's email" required>
                            @error('reporter_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Crime Reference Number -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Police Crime Reference Number </label></label>
                            <input type="text" name="crime_reference" class="form-control @error('crime_reference') is-invalid @enderror" value="{{ old('crime_reference') }}" placeholder="Enter reference number">
                            @error('crime_reference') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Date Received -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Date Received</label>
                            <input type="datetime-local" name="received_date" class="form-control @error('received_date') is-invalid @enderror" value="{{ old('received_date') }}">
                            @error('received_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Video Upload -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Upload Video</label>
                            <input type="file" name="video" class="form-control @error('video') is-invalid @enderror" accept="video/*">
                            @error('video') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <!-- Image Upload -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Upload Image</label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>


                        <!-- Audio Upload -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Upload Audio</label>
                            <input type="file" name="audio" class="form-control @error('audio') is-invalid @enderror" accept="audio/*">
                            @error('audio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Document Upload -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Upload Document</label>
                            <input type="file" name="document" class="form-control @error('document') is-invalid @enderror" accept=".pdf,.doc,.docx,.jpg,.png">
                            @error('document') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Description -->
                        <div class="col-md-12 mb-4">
                            <label>Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4"
                                      placeholder="Provide a detailed description including incident date and time" required>{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12 text-end mt-4">
                            <button type="submit" class="btn btn-success rounded">Submit ASB</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
  const blocks = @json(json_decode($blocks));
  const blockSelect = document.getElementById('block_id');
  const apartmentSelect = document.getElementById('apartment_id');
  const unitNumberInput = document.getElementById('unit_number');

  function clearApartments() {
    apartmentSelect.innerHTML = '<option value="" disabled selected>Select an apartment</option>';
    unitNumberInput.value = '';
  }

  blockSelect.addEventListener('change', function () {
    clearApartments();
    const selectedBlockId = parseInt(this.value);
    const block = blocks.find(b => b.id === selectedBlockId);

    if (block?.apartments) {
      block.apartments.forEach(apartment => {
        const option = document.createElement('option');
        option.value = apartment.id;
        const addr = apartment.address || '';
        let displayText = addr.trim() || `Apartment ${apartment.id}`;

        option.text = displayText.replace(/\w\S*/g, (w) => w.charAt(0).toUpperCase() + w.slice(1));
        apartmentSelect.appendChild(option);
      });
    }
  });

  apartmentSelect.addEventListener('change', function () {
    const block = blocks.find(b => b.id === parseInt(blockSelect.value));
    const apt = block?.apartments?.find(a => a.id === parseInt(this.value));
    unitNumberInput.value = apt?.unit_number || '';
  });

  $(document).ready(() => $('.select2').select2());
</script>
@endsection
