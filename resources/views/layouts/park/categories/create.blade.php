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
                        <h4 class="mb-sm-0">Park Category Manager</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('park.categories.index')}}">Park Categories</a></li>
                                <li class="breadcrumb-item active">Create Park Category</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <h4 class="card-title">Create New Park Category</h4>
                <p class="card-title-desc">Please provide the necessary details to create a new park category.</p>

                <form method="POST" action="{{ route('park.categories.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name">Name</label>
                            <input type="text" name="name" class="form-control" required placeholder="Enter category name" value="{{ old('name') }}">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="features">Features</label>
                            <textarea name="features" class="form-control" required rows="5" placeholder="Enter category features">{{ old('features') }}</textarea>
                            @error('features') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="mb-0">
                        <button type="submit" class="btn btn-success waves-effect waves-light me-1">
                            Submit
                        </button>
                        <a href="{{ route('park.categories.index') }}" class="btn btn-secondary waves-effect">Cancel</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection