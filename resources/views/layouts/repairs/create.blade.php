@extends('layouts.dashboard.landpage')

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 px-1">Create Repair</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('maintenance.index') }}">Repairs & Maintenance</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h6 class='text-center text-muted'>Maintenance Request Form</h6>

                <form method="POST" action="{{ route('maintenance.store') }}" id="maintenanceRequestForm">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6 col-xl-4">
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
                                   value="{{ old('unit_number') }}" required readonly>
                            @error('unit_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        @php
                            $fields = [
                                ['label' => 'Received Date', 'name' => 'received_date', 'type' => 'date'],
                                ['label' => 'Issue', 'name' => 'issue', 'type' => 'text'],
                                ['label' => 'Repair Type', 'name' => 'repair_type', 'type' => 'text'],
                                ['label' => 'Deadline Timeframe', 'name' => 'deadline_timeframe', 'type' => 'text'],
                                ['label' => 'Appointment Timeframe', 'name' => 'appointment_timeframe', 'type' => 'text'],
                                ['label' => 'Action Timeline', 'name' => 'action_timeline', 'type' => 'text'],
                                ['label' => 'Assigned To', 'name' => 'assigned_to', 'type' => 'text'],
                                ['label' => 'Ref', 'name' => 'ref', 'type' => 'text'],
                                ['label' => 'Due Date', 'name' => 'due_date', 'type' => 'date'],
                                ['label' => 'Appointment', 'name' => 'appointment', 'type' => 'date'],
                                ['label' => 'Completion Date', 'name' => 'completion_date', 'type' => 'date'],
                            ];
                        @endphp

                        @foreach ($fields as $field)
                            <div class="col-md-6 col-xl-4">
                                <label>{{ $field['label'] }} @if(in_array($field['name'], ['received_date', 'issue', 'repair_type', 'assigned_to']))<span class="text-danger">*</span>@endif</label>
                                <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" value="{{ old($field['name']) }}"
                                    class="form-control @error($field['name']) is-invalid @enderror"
                                    @if(in_array($field['name'], ['received_date', 'issue', 'repair_type', 'assigned_to'])) required @endif />
                                @error($field['name']) <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        @endforeach

                        <div class="col-md-12">
                            <label>Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" required>{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 col-xl-4">
                            <label>Progress <span class="text-danger">*</span></label>
                            <select name="progress" id="progress" class="form-select @error('progress') is-invalid @enderror" required>
                                <option value="" disabled selected>Select progress</option>
                                <option value="Pending" {{ old('progress') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="In Progress" {{ old('progress') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Completed" {{ old('progress') == 'Completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            @error('progress') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 col-xl-4">
                            <label>Status</label>
                            <input type="text" name="status" value="{{ old('status') ?? 'Open' }}" class="form-control" />
                        </div>

                        <div class="col-12 text-end mt-3">
                            <button type="submit" class="btn btn-success rounded">Submit Request</button>
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
        const unit = apartment.unit_number || '';
        const addr = apartment.address || '';
        let displayText = (addr).trim();

        if (!displayText) {
          displayText = `Apartment ${apartment.id}`;
        }

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
