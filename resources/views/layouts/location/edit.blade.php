@extends('layouts.dashboard.landpage')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Location</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('locations.index') }}">Locations</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <form action="{{ route('locations.update', $location->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Location</h4>
                    <p class="card-title-desc">Update the name of the location below.</p>

                    <div class="row mb-3">
                        <label for="name" class="col-sm-2 col-form-label">Location Name</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" id="name" name="name" placeholder="Enter location name" value="{{ old('name', $location->name) }}">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">Update</button>
                        <a href="{{ route('locations.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
