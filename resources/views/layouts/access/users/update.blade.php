@extends('layouts.dashboard.landpage')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">

            @if (session('success'))
                <div class="alert alert-success mb-3">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between px-3">
                        <h4 class="mb-sm-0">User Manager</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('access.users.index') }}">Users</a></li>
                                <li class="breadcrumb-item active">Edit User</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body px-4">
                <h4 class="card-title mb-3">Edit User</h4>
                <p class="card-title-desc mb-4">Update the user's information and role.</p>

                <form method="POST" action="{{ route('access.users.update', $admin->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="fname" class="form-label">First Name</label>
                            <input 
                                type="text" 
                                name="fname" 
                                id="fname" 
                                class="form-control" 
                                value="{{ old('fname', $admin->fName) }}" 
                                required
                            >
                            @error('fname')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="lname" class="form-label">Last Name</label>
                            <input 
                                type="text" 
                                name="lname" 
                                id="lname" 
                                class="form-control" 
                                value="{{ old('lname', $admin->lName) }}" 
                                required
                            >
                            @error('lname')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address</label>
                            <input 
                                type="email" 
                                name="email" 
                                id="email" 
                                class="form-control" 
                                value="{{ old('email', $admin->email) }}" 
                                required
                            >
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="role_id" class="form-label">Assign Role</label>
                            <select name="role_id" id="role_id" class="form-select" required>
                                <option value="">-- Select Role --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" 
                                        {{ old('role_id', $admin->role_id) == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
<input type="hidden" name="user_id" value="{{ $admin->id }}">
                    <div style="max-width: 500px;">
                        <button type="submit" class="btn btn-success waves-effect waves-light me-2">Update User</button>
                        <a href="{{ route('access.users.index') }}" class="btn btn-secondary waves-effect">Cancel</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
