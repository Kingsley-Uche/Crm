@extends('layouts.dashboard.landpage')

@section('content')
@if(session('success'))
    <div class="alert alert-success mb-4">{{ session('success') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Please correct the following errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 px-1">Create Branch</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('branches.index') }}">Branches</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Form Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm rounded-4 p-3">
            <div class="card-body p-4">
                <h6 class="text-center text-muted mb-4">Branch Registration Form</h6>
@php
    $branches = old('branches', [
        [
            'name' => '',
            'contact_email' => '',
            'contact_phone' => '',
            'address' => '',
            'manager_name' => '',
            'manager_email' => '',
            'manager_phone' => '',
            'account_name' => '',
            'account_number' => '',
            'bank_name' => '',
        ]
    ]);
@endphp

<form method="POST" action="{{ route('branches.store') }}">
    @csrf

    <div id="branches-container">

        @foreach($branches as $index => $branch)
        <div class="branch-item card border p-3 mb-4">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Branch #{{ $loop->iteration }}</h5>

                <button type="button"
                        class="btn btn-danger btn-sm remove-branch"
                        {{ count($branches) == 1 ? 'style=display:none' : '' }}>
                    <i class="fa fa-minus"></i>
                </button>
            </div>

            <div class="bg-light rounded mb-2">
                <h6 class="text-center">Branch Information</h6>
            </div>

            <div class="row g-3">

                <div class="col-md-6">
                    <label>Branch Name <span class="text-danger">*</span></label>

                    <input type="text"
                           name="branches[{{ $index }}][name]"
                           value="{{ old("branches.$index.name", $branch['name']) }}"
                           class="form-control @error("branches.$index.name") is-invalid @enderror"
                           required>

                    @error("branches.$index.name")
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label>Contact Email</label>

                    <input type="email"
                           name="branches[{{ $index }}][contact_email]"
                           value="{{ old("branches.$index.contact_email", $branch['contact_email']) }}"
                           class="form-control @error("branches.$index.contact_email") is-invalid @enderror">

                    @error("branches.$index.contact_email")
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label>Contact Phone</label>

                    <input type="text"
                           name="branches[{{ $index }}][contact_phone]"
                           value="{{ old("branches.$index.contact_phone", $branch['contact_phone']) }}"
                           class="form-control @error("branches.$index.contact_phone") is-invalid @enderror">

                    @error("branches.$index.contact_phone")
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label>Address</label>

                    <textarea name="branches[{{ $index }}][address]"
                              rows="2"
                              class="form-control @error("branches.$index.address") is-invalid @enderror">{{ old("branches.$index.address", $branch['address']) }}</textarea>

                    @error("branches.$index.address")
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

            </div>

            <hr>

            <div class="bg-light rounded mb-2">
                <h6 class="text-center">Manager Information</h6>
            </div>

            <div class="row g-3">

                <div class="col-md-4">
                    <label>Manager Name</label>

                    <input type="text"
                           name="branches[{{ $index }}][manager_name]"
                           value="{{ old("branches.$index.manager_name", $branch['manager_name']) }}"
                           class="form-control">
                </div>

                <div class="col-md-4">
                    <label>Manager Email</label>

                    <input type="email"
                           name="branches[{{ $index }}][manager_email]"
                           value="{{ old("branches.$index.manager_email", $branch['manager_email']) }}"
                           class="form-control">
                </div>

                <div class="col-md-4">
                    <label>Manager Phone</label>

                    <input type="text"
                           name="branches[{{ $index }}][manager_phone]"
                           value="{{ old("branches.$index.manager_phone", $branch['manager_phone']) }}"
                           class="form-control">
                </div>

            </div>

            <hr>

            <div class="bg-light rounded mb-2">
                <h6 class="text-center">Bank Information</h6>
            </div>

            <div class="row g-3">

                <div class="col-md-4">
                    <label>Account Name</label>

                    <input type="text"
                           name="branches[{{ $index }}][account_name]"
                           value="{{ old("branches.$index.account_name", $branch['account_name']) }}"
                           class="form-control">
                </div>

                <div class="col-md-4">
                    <label>Account Number</label>

                    <input type="text"
                           name="branches[{{ $index }}][account_number]"
                           value="{{ old("branches.$index.account_number", $branch['account_number']) }}"
                           class="form-control">
                </div>

                <div class="col-md-4">
                    <label>Bank Name</label>

                    <input type="text"
                           name="branches[{{ $index }}][bank_name]"
                           value="{{ old("branches.$index.bank_name", $branch['bank_name']) }}"
                           class="form-control">
                </div>

            </div>

        </div>
        @endforeach

    </div>

    <div class="mb-4">
        <button type="button" id="add-branch" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add Branch
        </button>
    </div>

    <div class="text-end">
        <button type="submit" class="btn btn-success">
            Save Branches
        </button>

        <a href="{{ route('branches.index') }}" class="btn btn-secondary">
            Cancel
        </a>
    </div>

</form>

            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    let branchIndex = 1;

    document.getElementById('add-branch').addEventListener('click', function () {

        const firstBranch = document.querySelector('.branch-item');
        const clone = firstBranch.cloneNode(true);

        clone.querySelectorAll('input, textarea').forEach(function(field) {

            field.value = '';

            field.name = field.name.replace(
                /\[\d+\]/,
                '[' + branchIndex + ']'
            );
        });

        clone.querySelector('h5').innerText = 'Branch #' + (branchIndex + 1);

        clone.querySelector('.remove-branch').style.display = 'inline-block';

        document.getElementById('branches-container').appendChild(clone);

        branchIndex++;
    });

    document.addEventListener('click', function(e) {

        if (e.target.closest('.remove-branch')) {

            const branches = document.querySelectorAll('.branch-item');

            if (branches.length > 1) {
                e.target.closest('.branch-item').remove();
            }
        }
    });

});
</script>
@endsection