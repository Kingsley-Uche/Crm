@extends('layouts.dashboard.landpage')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Categories</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('park.categories.index') }}">Categories</a></li>
                    <li class="breadcrumb-item active">List</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="card-title">All Categories</h4>
                        <p class="card-title-desc">Available park categories and their features</p>
                    </div>
                    <a href="{{ route('park.categories.create') }}" class="btn btn-success">
                        <i class="fa fa-plus text-white"></i> Add New Category
                    </a>
                </div>

                <div class="table-responsive">
                   <table class="table table-bordered table-striped dt-responsive nowrap w-100" id='selection-datatable'>
                        <thead>
                            <tr>
                                <th>SN</th>
                                <th>Category Name</th>
                                <th>Features</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                           
                                @foreach($categories as $index => $category)
                                    <tr>
                                        <th scope="row">{{ $index + 1 }}</th>
                                        <td>{{ $category->name }}</td>
                                        <td>{{ $category->features }}</td>
                                        <td>
                                            <a href="{{ route('park.categories.edit', $category->id) }}" class="btn btn-sm" title="Edit">
                                                <i class="fa fa-pen text-success"></i>
                                            </a>
                                            <form action="{{ route('park.categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm" title="Delete">
                                                    <i class="fas fa-trash-alt delete-btn text-danger"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection