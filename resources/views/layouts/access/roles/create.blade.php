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
                        <h4 class="mb-sm-0">Role Manager</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('access.roles.index') }}">Roles</a></li>
                                <li class="breadcrumb-item active">Create Role</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body px-4">
                <h4 class="card-title mb-3">Create New Role</h4>
                <p class="card-title-desc mb-4">Fill in the role details and assign permissions.</p>

                <form method="POST" action="{{ route('access.roles.store') }}">
                    @csrf

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Role Name</label>
                            <input 
                                type="text" 
                                name="name" 
                                id="name" 
                                placeholder="Enter role name"
                                class="form-control" 
                                required 
                                value="{{ old('name') }}"
                            >
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="description" class="form-label">Role Description</label>
                            <textarea
                                name="description"
                                id="description"
                                placeholder="Enter role description (optional)"
                                class="form-control"
                                rows="3"
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class='bg-light p-3 rounded'>
                            <label class="form-label d-block mb-2">Assign Permissions</label>
                            <div class="form-check form-switch mb-3">
                                <input 
                                    type="checkbox" 
                                    class="form-check-input" 
                                    id="selectAllPermissions"
                                >
                                <label class="form-check-label" for="selectAllPermissions">Select All Permissions</label>
                            </div>
                        </div>

                        <small class="text-danger d-block mb-3">*Activate each permission by toggling the switch on</small>
                        <div class="row">
                            @foreach ($permissions as $permission)
                                <div class="col-md-4 mb-3">
                                    <div class="form-check form-switch d-flex align-items-center">
                                        <input
                                            type="checkbox"
                                            class="form-check-input permission-checkbox"
                                            id="perm{{ $permission->id }}"
                                            name="permissions[]"
                                            value="{{ $permission->id }}"
                                            {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}
                                        />
                                        <label 
                                            class="form-check-label ms-2 permission-label {{ in_array($permission->id, old('permissions', [])) ? 'text-success' : '' }}"
                                            for="perm{{ $permission->id }}"
                                        >
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('permissions')
                            <small class="text-danger d-block mt-2">{{ $message }}</small>
                        @enderror
                    </div>

                    <div style="max-width: 500px;">
                        <button type="submit" class="btn btn-success waves-effect waves-light me-2">Create Role</button>
                        <a href="{{ route('access.roles.index') }}" class="btn btn-secondary waves-effect">Cancel</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    /* Style the checked switch background */
    .form-check-input.bg-success:checked {
        background-color: #198754 !important; /* Bootstrap's green */
        border-color: #198754 !important;
    }
</style>

<script>
    const selectAll = document.getElementById('selectAllPermissions');
    const checkboxes = document.querySelectorAll('.permission-checkbox');

    // Function to update checkbox background
    function updateCheckboxStyle(checkbox) {
        if (checkbox.checked) {
            checkbox.classList.add('bg-success');
            
        } else {
            checkbox.classList.remove('bg-success');
        }
    }

    // Initialize state and attach event listeners
    checkboxes.forEach(cb => {
        updateCheckboxStyle(cb);
        cb.addEventListener('change', () => updateCheckboxStyle(cb));
    });

    // Handle Select All toggle
    selectAll.addEventListener('change', function () {
        selectAll.classList.toggle('bg-success', this.checked);
        checkboxes.forEach(cb => {
            cb.checked = this.checked;
            updateCheckboxStyle(cb);
        });
    });
</script>
@endsection
