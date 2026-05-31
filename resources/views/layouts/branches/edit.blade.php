@extends('layouts.dashboard.landpage')

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 px-1">Edit Branch</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('branches.index') }}">Branches</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Form Card -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm rounded-4 p-3">
            <div class="card-body p-4">

                <form method="POST" action="{{ route('branches.update', $branch->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label>Branch Name</label>
                            <input type="text" name="name" value="{{ old('name', $branch->name) }}" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label>Email</label>
                            <input type="email" name="contact_email" value="{{ old('contact_email', $branch->contact_email) }}" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label>Phone</label>
                            <input type="text" name="contact_phone" value="{{ old('contact_phone', $branch->contact_phone) }}" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label>Address</label>
                            <textarea name="address" class="form-control">{{ old('address', $branch->address) }}</textarea>
                        </div>

                        <div class="col-md-4">
                            <label>Manager Name</label>
                            <input type="text" name="manager_name" value="{{ old('manager_name', $branch->manager_name) }}" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label>Manager Email</label>
                            <input type="email" name="manager_email" value="{{ old('manager_email', $branch->manager_email) }}" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label>Manager Phone</label>
                            <input type="text" name="manager_phone" value="{{ old('manager_phone', $branch->manager_phone) }}" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label>Account Name</label>
                            <input type="text" name="account_name" value="{{ old('account_name', $branch->account_name) }}" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label>Account Number</label>
                            <input type="text" name="account_number" value="{{ old('account_number', $branch->account_number) }}" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label>Bank Name</label>
                            <input type="text" name="bank_name" value="{{ old('bank_name', $branch->bank_name) }}" class="form-control">
                        </div>

                    </div>

                    <div class="mt-4 text-end">
                        <button class="btn btn-success">Update Branch</button>
                        <a href="{{ route('branches.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

@endsection