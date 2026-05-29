@extends('layouts.dashboard.landpage')

@section('content')

@if (session('success'))
    <div class="alert alert-success mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="row mb-4">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">

            <h4 class="mb-sm-0 px-1">Edit Brand Details</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('brand.index') }}">Brands</a>
                    </li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">

        <div class="card shadow-sm rounded-4 mb-5">

            <div class="card-body p-4">

                <h5 class="text-center text-muted mb-4">
                    Edit Brand Details
                </h5>

                <form action="{{ route('brand.update', $brand->id) }}"
                      method="POST"
                      enctype="multipart/form-data">

                    @csrf
                    @method('PUT')

                    <div class="row g-4">

                        <!-- Name -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Brand Name *</label>
                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   value="{{ old('name', $brand->name) }}"
                                   required>
                        </div>

                        <!-- Website -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Website URL</label>
                            <input type="url"
                                   name="website_url"
                                   class="form-control"
                                   value="{{ old('website_url', $brand->website_url) }}">
                        </div>

                        <!-- LOGO FILE UPLOAD (FIXED) -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Logo</label>

                            <input type="file"
                                   name="logo"
                                   class="form-control"
                                   accept="image/*">

                            {{-- Current Logo Preview --}}
                            @if($brand->logo_url)
                                <div class="mt-2">
                                    <img src="{{ asset($brand->logo_url) }}"
                                         style="width:70px;height:70px;object-fit:cover;border-radius:8px;">
                                </div>
                            @endif
                        </div>

                        <!-- Contact Email -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Contact Email</label>
                            <input type="email"
                                   name="contact_email"
                                   class="form-control"
                                   value="{{ old('contact_email', $brand->contact_email) }}">
                        </div>

                        <!-- Contact Phone -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Contact Phone</label>
                            <input type="text"
                                   name="contact_phone"
                                   class="form-control"
                                   value="{{ old('contact_phone', $brand->contact_phone) }}">
                        </div>

                        <!-- Slug -->
                        <div class="col-md-6 col-xl-4 mb-3">
                            <label>Slug</label>
                            <input type="text"
                                   name="slug"
                                   class="form-control"
                                   value="{{ old('slug', $brand->slug) }}">
                        </div>

                        <!-- Meta Title -->
                        <div class="col-md-6 mb-3">
                            <label>Meta Title</label>
                            <input type="text"
                                   name="meta_title"
                                   class="form-control"
                                   value="{{ old('meta_title', $brand->meta_title) }}">
                        </div>

                        <!-- Meta Keywords -->
                        <div class="col-md-6 mb-3">
                            <label>Meta Keywords</label>
                            <input type="text"
                                   name="meta_keywords"
                                   class="form-control"
                                   value="{{ old('meta_keywords', $brand->meta_keywords) }}">
                        </div>

                        <!-- Meta Description -->
                        <div class="col-md-12 mb-3">
                            <label>Meta Description</label>
                            <textarea name="meta_description"
                                      rows="3"
                                      class="form-control">{{ old('meta_description', $brand->meta_description) }}</textarea>
                        </div>

                        <!-- OG Title -->
                        <div class="col-md-6 mb-3">
                            <label>OG Title</label>
                            <input type="text"
                                   name="og_title"
                                   class="form-control"
                                   value="{{ old('og_title', $brand->og_title) }}">
                        </div>

                        <!-- OG Image -->
                        <div class="col-md-6 mb-3">
                            <label>OG Image URL</label>
                            <input type="text"
                                   name="og_image"
                                   class="form-control"
                                   value="{{ old('og_image', $brand->og_image) }}">
                        </div>

                        <!-- Description -->
                        <div class="col-md-12 mb-3">
                            <label>Description</label>
                            <textarea name="description"
                                      rows="5"
                                      class="form-control">{{ old('description', $brand->description) }}</textarea>
                        </div>

                        <!-- Submit -->
                        <div class="col-12 text-end mt-4">
                            <button type="submit" class="btn btn-primary rounded">
                                Update Brand
                            </button>
                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>
</div>

@endsection