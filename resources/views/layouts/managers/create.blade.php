@extends('layouts.dashboard.landpage')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Create Property Manager</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('managers.index') }}">Property Managers</a>
                    </li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<div class="card">
    <div class="card-body">

        <form method="POST" action="{{ route('managers.store') }}">
            @csrf

            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Name</label>
                <div class="col-sm-10">
                    <input
                        type="text"
                        name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
                        required
                    >

                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Email</label>

                <div class="col-sm-10">
                    <input
                        type="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        required
                    >

                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Phone</label>

                <div class="col-sm-10">
                    <input
                        type="text"
                        name="phone"
                        class="form-control @error('phone') is-invalid @enderror"
                        value="{{ old('phone') }}"
                    >

                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Password</label>

                <div class="col-sm-10">
                    <input
                        type="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        required
                    >

                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Confirm Password</label>

                <div class="col-sm-10">
                    <input
                        type="password"
                        name="password_confirmation"
                        class="form-control"
                        required
                    >
                </div>
            </div>

            <div class="row">
                <div class="col-sm-10 offset-sm-2">
                    <a href="{{ route('managers.index') }}"
                       class="btn btn-secondary">
                        Cancel
                    </a>

                    <button type="submit"
                            class="btn btn-success">
                        Create Manager
                    </button>
                </div>
            </div>

        </form>

    </div>
</div>
@endsection