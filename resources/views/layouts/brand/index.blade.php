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
                    <li class="breadcrumb-item active">Details</li>
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
                        <p class="card-title-desc">Manage your brand details</p>
                    </div>

                    
                    @if(!$brands)
                        <a href="{{ route('brand.create') }}" class="btn btn-success">
                            <i class="fa fa-plus text-white"></i> Create Brand
                        </a>
                    @endif

                </div>

                @if($brands)

                    <div class="table-responsive">

                        <table class="table table-hover mb-0">

                            <tbody>

                                <tr>
                                    <th width="200">Logo</th>
                                    <td>
                                        @if($brands->logo_url)
                                            <img src="{{ asset($brands->logo_url) }}"
                                                 width="80"
                                                 height="80"
                                                 style="object-fit:cover;border-radius:6px;">
                                        @else
                                            <span class="text-muted">No Logo</span>
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th>Name</th>
                                    <td>{{ $brands->name }}</td>
                                </tr>

                                <tr>
                                    <th>Website</th>
                                    <td>
                                        @if($brands->website_url)
                                            <a href="{{ $brands->website_url }}" target="_blank">
                                                {{ $brands->website_url }}
                                            </a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th>Email</th>
                                    <td>{{ $brands->contact_email ?? 'N/A' }}</td>
                                </tr>

                                <tr>
                                    <th>Indexed</th>
                                    <td>
                                        @if($brands->is_indexed)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-danger">No</span>
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th>Created</th>
                                    <td>{{ $brands->created_at?->format('d M Y') }}</td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                    <div class="mt-4 d-flex gap-2">

                        <a href="{{ route('brand.edit', $brands->id) }}"
                           class="btn">
                            <i class="fa fa-pen text-primary"></i>Edit
                        </a>

                        <form action="{{ route('brand.destroy', $brands->id) }}"
                              method="POST">

                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn delete-btn">
                                <i class="fas fa-trash-alt text-danger"></i> Delete
                            </button>

                        </form>

                    </div>

                @else

                    <div class="alert alert-info">
                        No brand has been created yet.
                    </div>

                @endif

            </div>

        </div>

    </div>
</div>

@endsection