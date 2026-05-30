@extends('layouts.dashboard.landpage')

@section('content')

@if(session('success'))
    <div class="alert alert-success mb-4">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger mb-4">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card shadow-sm border-0 rounded-4">

    <div class="card-header bg-white border-bottom py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1 fw-bold">Edit Brand</h5>
                <small class="text-muted">
                    Update branding, contact information and SEO settings.
                </small>
            </div>

            @if(!empty($brand->logo_url))
                <img src="{{ asset($brand->logo_url) }}"
                     alt="Brand Logo"
                     class="rounded shadow-sm"
                     style="width:60px;height:60px;object-fit:cover;">
            @endif
        </div>
    </div>

    <div class="card-body p-4">

        <form action="{{ route('brand.update', $brand->id) }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf
            @method('PUT')

            {{-- BASIC INFORMATION --}}
            <div class="mb-5">

                <h6 class="fw-bold text-primary border-bottom pb-2 mb-4">
                    Basic Information
                </h6>

                <div class="row g-4">

                    <div class="col-lg-8">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">
                                    Brand Name
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                       name="name"
                                       class="form-control"
                                       value="{{ old('name', $brand->name) }}"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Website URL
                                </label>

                                <input type="url"
                                       name="website_url"
                                       class="form-control"
                                       value="{{ old('website_url', $brand->website_url) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Slug
                                </label>

                                <input type="text"
                                       name="slug"
                                       class="form-control"
                                       value="{{ old('slug', $brand->slug) }}">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">
                                    Description
                                </label>

                                <textarea name="description"
                                          rows="5"
                                          class="form-control">{{ old('description', $brand->description) }}</textarea>
                            </div>

                        </div>

                    </div>

                    <div class="col-lg-4">

                        <div class="border rounded-3 p-3 bg-light">

                            <h6 class="fw-semibold mb-3">
                                Brand Appearance
                            </h6>

                            @if(!empty($brand->logo_url))
                                <div class="text-center mb-3">
                                    <img src="{{ asset($brand->logo_url) }}"
                                         alt="Logo"
                                         class="img-thumbnail shadow-sm"
                                         style="width:120px;height:120px;object-fit:cover;">
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label">
                                    Upload New Logo
                                </label>

                                <input type="file"
                                       name="logo"
                                       class="form-control"
                                       accept="image/*">
                            </div>

                            <div>
                                <label class="form-label">
                                    Brand Color
                                </label>

                                <div class="d-flex align-items-center gap-3">
                                    <input type="color"
                                           name="brand_color"
                                           class="form-control form-control-color"
                                           value="{{ old('brand_color', $brand->brand_color ?? '#074784') }}">

                                    <span class="text-muted small">
                                        Primary brand color
                                    </span>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

            {{-- CONTACT INFORMATION --}}
            <div class="mb-5">

                <h6 class="fw-bold text-primary border-bottom pb-2 mb-4">
                    Contact Information
                </h6>

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">
                            Contact Email
                        </label>

                        <input type="email"
                               name="contact_email"
                               class="form-control"
                               value="{{ old('contact_email', $brand->contact_email) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Contact Phone
                        </label>

                        <input type="text"
                               name="contact_phone"
                               class="form-control"
                               value="{{ old('contact_phone', $brand->contact_phone) }}">
                    </div>

                </div>

            </div>

            {{-- SEO SETTINGS --}}
            <div class="mb-4">

                <h6 class="fw-bold text-primary border-bottom pb-2 mb-4">
                    SEO & Social Sharing
                </h6>

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">
                            Meta Title
                        </label>

                        <input type="text"
                               name="meta_title"
                               class="form-control"
                               value="{{ old('meta_title', $brand->meta_title) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Meta Keywords
                        </label>

                        <input type="text"
                               name="meta_keywords"
                               class="form-control"
                               value="{{ old('meta_keywords', $brand->meta_keywords) }}">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">
                            Meta Description
                        </label>

                        <textarea name="meta_description"
                                  rows="3"
                                  class="form-control">{{ old('meta_description', $brand->meta_description) }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            OG Title
                        </label>

                        <input type="text"
                               name="og_title"
                               class="form-control"
                               value="{{ old('og_title', $brand->og_title) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            OG Image URL
                        </label>

                        <input type="text"
                               name="og_image"
                               class="form-control"
                               value="{{ old('og_image', $brand->og_image) }}">
                    </div>

                </div>

            </div>

            {{-- ACTION BUTTONS --}}
            <div class="border-top pt-4 d-flex justify-content-end gap-2">

                <a href="{{ route('brand.index') }}"
                   class="btn btn-light">
                    Cancel
                </a>

                <button type="submit"
                        class="btn btn-primary px-4">
                    <i class="ri-save-line me-1"></i>
                    Update Brand
                </button>

            </div>

        </form>

    </div>

</div>

@endsection