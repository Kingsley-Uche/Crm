@extends('layouts.dashboard.landpage')

@section('content')
<style>
    th, td {
        font-size: 12px;
    }
</style>

<!-- Success Message -->
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
   
<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Tenancy Type</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Utility</a></li>
                    <li class="breadcrumb-item active">Tenancy Type</li>
                </ol>
            </div>
        </div>
    </div>
</div>
                
<!-- Form Section -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Manage Tenancy Types</h4>
                <form action="{{route('tenancy.store')}}" method="POST">
                    @csrf
                    <div id="tenancy-container">
                        <div class="row mb-3 tenancy-item">
                            <label for="tenancy_type[]" class="col-sm-2 col-form-label">Tenancy Type</label>
                            <div class="col-sm-8">
                                <input class="form-control" type="text" name="tenancy_type[]" placeholder="Enter tenancy type">
                            </div>
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-success add-tenancy"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success float-right">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelector('.add-tenancy').addEventListener('click', function () {
            let container = document.getElementById('tenancy-container');
            let newField = document.createElement('div');
            newField.classList.add('row', 'mb-3', 'tenancy-item');
            newField.innerHTML = `
                <label for="tenancy_type[]" class="col-sm-2 col-form-label">Tenancy Type</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" name="tenancy_type[]" placeholder="Enter tenancy type">
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-danger remove-tenancy"><i class="fas fa-trash"></i></button>
                </div>`;
            container.appendChild(newField);
        });
        
        document.getElementById('tenancy-container').addEventListener('click', function (event) {
            if (event.target.closest('.remove-tenancy')) {
                event.target.closest('.tenancy-item').remove();
            }
        });
    });
</script>
@endsection