@extends('layouts.dashboard.landpage')

@section('content')

@if (session('success'))
    <div class="alert alert-success mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white border-bottom">
        <h5 class="mb-0 fw-semibold">Create Brand</h5>
        <small class="text-muted">
            Configure your organization's branding and SEO information.
        </small>
    </div>

    <div class="card-body p-4">
        <form action="{{ route('brand.store') }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf

            {{-- Basic Information --}}
            <div class="mb-4">
                <h6 class="fw-bold text-bold border-bottom pb-2">
                    Basic Information
                </h6>

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">
                            Brand Name <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               required>

                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Website URL</label>
                        <input type="url"
                               name="website_url"
                               class="form-control"
                               value="{{ old('website_url') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Logo Upload</label>
                        <input type="file"
                               name="logo"
                               class="form-control @error('logo') is-invalid @enderror"
                               accept="image/*">

                        @error('logo')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Brand Color</label>

                        <div class="d-flex align-items-center gap-3">
                            <input type="color"
                                   name="brand_color"
                                   class="form-control form-control-color"
                                   value="{{ old('brand_color', '#074784') }}">

                            <span class="text-muted small">
                                Select your primary brand color
                            </span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Slug</label>
                        <input type="text"
                               name="slug"
                               class="form-control"
                               value="{{ old('slug') }}"
                               placeholder="auto-generated if left blank">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Description</label>
                        <textarea name="description"
                                  rows="3"
                                  class="form-control">{{ old('description') }}</textarea>
                    </div>

                </div>
            </div>

            {{-- Contact Information --}}
            <div class="mb-4">
                <h6 class="fw-bold text- border-bottom pb-2">
                    Contact Information
                </h6>

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Contact Email</label>
                        <input type="email"
                               name="contact_email"
                               class="form-control"
                               value="{{ old('contact_email') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Contact Phone</label>
                        <input type="text"
                               name="contact_phone"
                               class="form-control"
                               value="{{ old('contact_phone') }}">
                    </div>

                </div>
            </div>

            {{-- SEO Settings --}}
            <div class="mb-4">
                <h6 class="fw-bold text-bold border-bottom pb-2">
                    SEO & Social Media
                </h6>

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Meta Title</label>
                        <input type="text"
                               name="meta_title"
                               class="form-control"
                               value="{{ old('meta_title') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text"
                               name="meta_keywords"
                               class="form-control"
                               value="{{ old('meta_keywords') }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description"
                                  rows="3"
                                  class="form-control">{{ old('meta_description') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">OG Title</label>
                        <input type="text"
                               name="og_title"
                               class="form-control"
                               value="{{ old('og_title') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">OG Image URL</label>
                        <input type="text"
                               name="og_image"
                               class="form-control"
                               value="{{ old('og_image') }}">
                    </div>

                </div>
            </div>

            <div class="border-top pt-4 d-flex justify-content-end gap-2">
                <a href="{{ route('brand.index') }}"
                   class="btn btn-light">
                    Cancel
                </a>

                <button type="submit"
                        class="btn btn-primary px-4">
                    <i class="ri-save-line me-1"></i>
                    Create Brand
                </button>
            </div>

        </form>
    </div>
</div>

@endsection