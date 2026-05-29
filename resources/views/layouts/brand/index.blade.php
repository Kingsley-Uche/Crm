@extends('layouts.dashboard.landpage')

@section('content')

<div class="row">
    <div class="col-12">

        <div class="page-title-box d-sm-flex align-items-center justify-content-between">

            <h4 class="mb-sm-0">Brand Details</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('brand.index') }}">Brand</a>
                    </li>
                    <li class="breadcrumb-item active">List</li>
                </ol>
            </div>

        </div>

    </div>
</div>

<div class="row">
    <div class="col-lg-12">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="card">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <div>
                        <h4 class="card-title">Brand Profile</h4>
                        <p class="card-title-desc">Manage your single brand details</p>
                    </div>

                    <a href="{{ route('brand.create') }}" class="btn btn-success">
                        <i class="fa fa-plus text-white"></i> Create Brand
                    </a>

                </div>

                <div class="table-responsive">

                    <table class="table table-hover mb-0">

                        <thead>
                            <tr>
                                <th>SN</th>
                                <th>Logo</th>
                                <th>Name</th>
                                <th>Website</th>
                                <th>Email</th>
                                <th>Indexed</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($brands as $index => $brand)

                                <tr>

                                    <th scope="row">
                                        {{ $index + 1 }}
                                    </th>

                                    <td>
                                        @if($brand->logo_url)
                                            <img src="{{ asset($brand->logo_url) }}"
                                                 width="45"
                                                 height="45"
                                                 style="object-fit:cover;border-radius:6px;">
                                        @else
                                            <span class="text-muted">No Logo</span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $brand->name }}
                                    </td>

                                    <td>
                                        @if($brand->website_url)
                                            <a href="{{ $brand->website_url }}" target="_blank">
                                                Visit
                                            </a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $brand->contact_email ?? 'N/A' }}
                                    </td>

                                    <td>
                                        @if($brand->is_indexed)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-danger">No</span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $brand->created_at->format('d M Y') }}
                                    </td>

                                    <td>

                                        <div class="d-flex gap-2">

                                            <a href="{{ route('brand.edit', $brand->id) }}"
                                               class="btn btn-sm">
                                               <i class="fa fa-pen text-success"></i>
                                                Edit
                                            </a>

                                            <form action="{{ route('brand.destroy', $brand->id) }}"
                                                  method="POST">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-sm" title="Delete">
                                                <i class="fas fa-trash-alt delete-btn text-danger"></i>
                                            </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="8" class="text-center">
                                        No brand found.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="mt-3">
                    {{ $brands->links() }}
                </div>

            </div>

        </div>

    </div>
</div>

@endsection