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
                        <h4 class="mb-sm-0">Park Location Manager</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('park.models.index') }}">Park Locations</a></li>
                                <li class="breadcrumb-item active">Create Park</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <h4 class="card-title">Create New Park</h4>
                <p class="card-title-desc">Please provide the necessary details to create a new park location.</p>

                <form method="POST" action="{{ route('park.models.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name">Park Name</label>
                            <input type="text" name="name" class="form-control" required placeholder="Enter park name" value="{{ old('name') }}">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="location">Location</label>
                            <input type="text" name="location" class="form-control" required placeholder="Enter park location" value="{{ old('location') }}">
                            @error('location') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="address">Address</label>
                            <textarea name="address" class="form-control" required rows="3" placeholder="Enter park address">{{ old('address') }}</textarea>
                            @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="category_id">Park Category</label>
                            <select name="category_id" class="form-control" required>
                                <option value="">-- Select Category --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="mb-0">
                        <button type="submit" class="btn btn-success waves-effect waves-light me-1">
                            Submit
                        </button>
                        <a href="{{ route('park.models.index') }}" class="btn btn-secondary waves-effect">Cancel</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
