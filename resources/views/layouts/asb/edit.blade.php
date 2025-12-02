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
            <h4 class="mb-sm-0 px-1">Edit Anti-Social Behaviour</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('asb.index') }}">ASB</a></li>
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
                <h6 class='text-center text-muted mb-4'>Edit ASB - {{ $asb->ref }}</h6>

                <form action="{{ route('asb.update', $asb->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <!-- Block -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Block Name <span class="text-danger">*</span></label>
                            <select name="block_id" id="block_id" class="form-select select2" required>
                                @foreach(json_decode($blocks) as $block)
                                    <option value="{{ $block->id }}" {{ $block->id == $asb->block_id ? 'selected' : '' }}>
                                        {{ ucwords($block->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Apartment -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Apartment <span class="text-danger">*</span></label>
                            <select name="apartment_id" id="apartment_id" class="form-select select2" required>
                                <option value="{{ $asb->apartment_id }}" selected>
                                    Apartment ID: {{ $asb->apartment_id }}
                                </option>
                            </select>
                        </div>

                        <!-- Unit Number -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Unit Number <span class="text-danger">*</span></label>
                            <input type="text" name="unit_number" id="unit_number" class="form-control" value="{{ $asb->unit_number }}" readonly required>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Status</label>
                            <input type="text" name="status" class="form-control" value="{{ $asb->status }}">
                        </div>

                        <!-- Appointment -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Appointment</label>
                            <input type="date" name="appointment" class="form-control" value="{{ $asb->appointment }}">
                        </div>

                        <!-- Completion Date -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Completion Date</label>
                            <input type="date" name="completion_date" class="form-control" value="{{ $asb->completion_date }}">
                        </div>

                        <!-- Due Date -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Due Date</label>
                            <input type="date" name="due_date" class="form-control" value="{{ $asb->due_date }}">
                        </div>

                        <!-- Assigned To -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Assigned To</label>
                            <input type="text" name="assigned_to" class="form-control" value="{{ $asb->assigned_to }}">
                        </div>

                        <!-- Reference -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Reference</label>
                            <input type="text" name="ref" class="form-control" value="{{ $asb->ref }}" readonly>
                        </div>

                        <!-- Reporter Email -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Reporter Email <span class="text-danger">*</span></label>
                            <input type="email" name="reporter_email" class="form-control" value="{{ $asb->reporter_email }}" required>
                        </div>

                        <!-- Crime Reference -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Crime Reference</label>
                            <input type="text" name="crime_reference" class="form-control" value="{{ $asb->crime_reference }}">
                        </div>

                        <!-- Received Date -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Received Date</label>
                            <input type="datetime-local" name="received_date" class="form-control" value="{{ \Carbon\Carbon::parse($asb->received_date)->format('Y-m-d\TH:i') }}">
                        </div>

                        <!-- Uploads -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            @if($asb->image)
                                <img src="{{ asset('storage/'.$asb->image) }}" alt="Current Image" class="img-thumbnail mt-2" width="150">
                            @endif
                        </div>

                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Video</label>
                            <input type="file" name="video" class="form-control" accept="video/*">
                            @if($asb->video)
                                <video controls width="150" class="mt-2">
                                    <source src="{{ asset('storage/'.$asb->video) }}">
                                </video>
                            @endif
                        </div>

                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Audio</label>
                            <input type="file" name="audio" class="form-control" accept="audio/*">
                            @if($asb->audio)
                                <audio controls class="mt-2">
                                    <source src="{{ asset('storage/'.$asb->audio) }}">
                                </audio>
                            @endif
                        </div>

                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Document</label>
                            <input type="file" name="document" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png">
                            @if($asb->document)
                                <a href="{{ asset('storage/'.$asb->document) }}" target="_blank" class="d-block mt-2">View current document</a>
                            @endif
                        </div>

                        <!-- Issue -->
                        <div class="col-md-12 mb-3">
                            <label>Issue</label>
                            <input type="text" name="issue" class="form-control" value="{{ $asb->issue }}">
                        </div>

                        <!-- Description -->
                        <div class="col-md-12 mb-3">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="4" required>{{ $asb->description }}</textarea>
                        </div>

                        <!-- Submit -->
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-success rounded">Update ASB</button> 
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
    const unitInput = document.getElementById('unit_number');

    function populateApartments() {
        const selectedBlock = blocks.find(b => b.id == blockSelect.value);
        apartmentSelect.innerHTML = '';
        if (selectedBlock?.apartments) {
            selectedBlock.apartments.forEach(ap => {
                const option = document.createElement('option');
                option.value = ap.id;
                option.textContent = ap.address || `Apartment ${ap.id}`;
                if ({{ $asb->apartment_id }} == ap.id) {
                    option.selected = true;
                    unitInput.value = ap.unit_number;
                }
                apartmentSelect.appendChild(option);
            });
        }
    }

    blockSelect.addEventListener('change', populateApartments);
    window.addEventListener('DOMContentLoaded', populateApartments);
</script>
@endsection
