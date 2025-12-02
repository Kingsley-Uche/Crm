@extends('layouts.app')

@section('content')
<section class="vh-80 mt-5">
    <div class="container-fluid h-custom">
        <div class="row d-flex justify-content-center align-items-center">

            <!-- Left Column: Logo -->
            <div class="col-md-8 col-lg-4 col-xl-4 text-center">
                <img src="{{ url('system_images/ctrlogo.png') }}" class="img-fluid" alt="Tenant Management Organization">
            </div>

            <!-- Right Column: Password Reset Form -->
            <div class="col-md-9 col-lg-8 col-xl-5 offset-xl-1">
                <div class="card">
                    <div class="card-body">
                        <h4 class="text-muted text-center font-size-18"><b>Password Reset</b></h4>

                        <div class="p-3">
                            <form method="POST" action="{{ route('admin.password.email') }}">
                                @csrf

                                <p class="text-muted mb-3">
                                    Enter your email address. If it exists on our platform, a new password will be generated and sent to you.
                                </p>

                                <!-- Email Input -->
                                <div class="form-group mb-3">
                                    <input class="form-control @error('email') is-invalid @enderror"
                                           type="email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           required
                                           placeholder="Enter your email">

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- Submit Button -->
                                <div class="form-group text-center mt-3">
                                    <button class="btn btn-success w-100" type="submit">
                                        Send Reset Email
                                    </button>
                                </div>

                                <!-- Back to login -->
                                <div class="form-group text-center mt-3">
                                    <a href="{{ route('admin.login') }}" class="text-muted">
                                        <i class="mdi mdi-arrow-left"></i> Back to Login
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="d-flex flex-column flex-md-row text-center text-md-start justify-content-between mt-4 py-4 px-4 px-xl-5 bg-success text-white position-fixed w-100 bottom-0">
        <div>
            <script>document.write(new Date().getFullYear())</script> © CTR TRIANGLE TMO.
        </div>
    </div>
</section>
@endsection
