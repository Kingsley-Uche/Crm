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
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="row px-3 py-3">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Permit Manager</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('park.permits.index') }}">Permits</a></li>
                                <li class="breadcrumb-item active">Edit Permit</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <h4 class="card-title">Edit Permit</h4>
                <p class="card-title-desc">Update the details below to modify the permit.</p>

                <form method="POST" action="{{ route('park.permits.update', $permit->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name">First Name</label>
                            <input type="text" name="first_name" id="first_name" class="form-control" required value="{{ old('first_name', $permit->fname) }}">
                            @error('first_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="last_name">Last Name</label>
                            <input type="text" name="last_name" id="last_name" class="form-control" required value="{{ old('last_name', $permit->lname) }}">
                            @error('last_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control" required value="{{ old('email', $permit->email) }}">
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="phone">Phone Number</label>
                            <input type="text" name="phone" id="phone" class="form-control" required value="{{ old('phone', $permit->phone) }}">
                            @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Permit Name -->
                        <div class="col-md-6 mb-3">
                            <label for="permit_name">Permit Name</label>
                            <input type="text" name="permit_name" class="form-control" required placeholder="Enter permit name" value="{{ old('permit_name', $permit->permit_name) }}">
                            @error('permit_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Park Dropdown -->
                        <div class="col-md-6 mb-3">
                            <label for="park_id">Park</label>
                            <select name="park_id" class="form-control" required>
                                <option value="">-- Select Park --</option>
                                @foreach ($parks as $park)
                                    <option value="{{ $park->id }}" {{ old('park_id', $permit->park_model_id) == $park->id ? 'selected' : '' }}>{{ $park->name }}</option>
                                @endforeach
                            </select>
                            @error('park_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Category Dropdown -->
                        <div class="col-md-6 mb-3">
                            <label for="category_id">Category</label>
                            <select name="category_id" class="form-control" required>
                                <option value="">-- Select Category --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $permit->park_category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Start Date -->
                        <div class="col-md-6 mb-3">
                            <label for="start_date">Start Date</label>
                            <input type="date" name="start_date" class="form-control" required value="{{ old('start_date', \Carbon\Carbon::parse($permit->start_time)->format('Y-m-d')) }}">
                            @error('start_date') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- End Date -->
                        <div class="col-md-6 mb-3">
                            <label for="end_date">End Date</label>
                            <input type="date" name="end_date" class="form-control" required value="{{ old('end_date', \Carbon\Carbon::parse($permit->end_time)->format('Y-m-d')) }}">
                            @error('end_date') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Fee -->
                        <div class="col-md-6 mb-3">
                            <label for="fee">Fee</label>
                            <input type="text" name="fee" id="fee" class="form-control" required value="{{ old('fee', $permit->fee) }}">
                            @error('fee') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Taxes -->
                        <div class="col-md-12 mb-4">
                            <label>Applicable Taxes</label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach ($taxes as $tax)
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" 
                                               id="tax{{ $tax->id }}" 
                                               name="taxes[]" 
                                               value="{{ $tax->id }}" 
                                               switch="info" 
                                               {{ in_array($tax->id, old('taxes', $permit->taxes->pluck('id')->toArray())) ? 'checked' : '' }}/>
                                        <label for="tax{{ $tax->id }}" data-on-label="Yes" data-off-label="No" class="mb-0 me-2"></label>
                                        <span class="ms-1">{{ $tax->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                            @error('taxes') 
                                <small class="text-danger">{{ $message }}</small> 
                            @enderror
                        </div>
                    </div>

                    <div class="mb-0">
                        <button type="submit" class="btn btn-success waves-effect waves-light me-1">Update</button>
                        <a href="{{ route('park.permits.index') }}" class="btn btn-secondary waves-effect">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection