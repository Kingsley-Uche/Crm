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
                        <h4 class="mb-sm-0">Tax Manager</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('park.taxes.index') }}">Taxes</a></li>
                                <li class="breadcrumb-item active">Edit Tax</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <h4 class="card-title">Edit Tax</h4>
                <p class="card-title-desc">Update the tax details as needed.</p>

                <form method="POST" action="{{ route('park.taxes.update', $tax->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name">Tax Name</label>
                            <input type="text" name="name" class="form-control" required 
                                   placeholder="Enter tax name" 
                                   value="{{ old('name', $tax->name) }}">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="rate">Percentage (%)</label>
                            <input type="number" name="rate" class="form-control" required 
                                   placeholder="Enter tax percentage" 
                                   step="0.01" min="0" max="100" 
                                   value="{{ old('rate', $tax->rate) }}">
                            @error('rate') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description">Description (optional)</label>
                        <textarea name="description" class="form-control" 
                                  placeholder="Enter description">{{ old('description', $tax->description) }}</textarea>
                        @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-0">
                        <button type="submit" class="btn btn-primary waves-effect waves-light me-1">
                            Update
                        </button>
                        <a href="{{ route('park.taxes.index') }}" class="btn btn-secondary waves-effect">Cancel</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
