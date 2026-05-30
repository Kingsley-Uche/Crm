@extends('layouts.app')

@section('content')

<section class="vh-80 mt-5">
    @php
    $brand = session('brand_details') ?? cache('brand_details');

    $brandName = $brand['name'] ?? config('app.name');
    $brandLogo = $brand['logo_url'] ?? asset('system_images/ctrlogo.png');
    $brandColor = $brand['brand_color'] ?? '#074784';
@endphp
    <div class="container-fluid h-custom">
        <div class="row d-flex justify-content-center align-items-center">
            <!-- Left Column: Image -->
            <div class="col-md-8 col-lg-4 col-xl-4 text-center">
                <img src="{{ asset($brand['logo_url']) }}" class="img-fluid" alt="{{ $brandName }} Logo" style="max-width: 150px;">
            </div>

            <!-- Right Column: Upcube-Styled Login Form -->
            <div class="col-md-9 col-lg-8 col-xl-5 offset-xl-1">
                <div class="card">
                    <div class="card-body">
                        <h4 class="text-muted text-center font-size-18"><b>Welcome</b></h4>

                        <div class="p-3">
                            <form class="form-horizontal mt-3" method="POST" action="{{ route('admin.login.submit') }}">
                                @csrf

                                <!-- Username/Email Input -->
                                <div class="form-group mb-3 row">
                                    <div class="col-12">
                                        <input class="form-control @error('email') is-invalid @enderror" 
                                               type="email" 
                                               name="email" 
                                               id="email" 
                                               value="{{ old('email') }}" 
                                               required 
                                               placeholder="Email">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Password Input -->
                                <div class="form-group mb-3 row">
                                    <div class="col-12">
                                        <input class="form-control @error('password') is-invalid @enderror" 
                                               type="password" 
                                               name="password" 
                                               id="password" 
                                               required 
                                               placeholder="Password">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Remember Me Checkbox -->
                                <div class="form-group mb-3 row">
                                    <div class="col-12">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" 
                                                   class="custom-control-input" 
                                                   name="remember" 
                                                   id="remember" 
                                                   {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-label ms-1" for="remember">Remember me</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="form-group mb-3 text-center row mt-3 pt-1">
                                    <div class="col-12">
                                        <button class="btn btn-success w-100 waves-effect waves-light" type="submit">Log In</button>
                                    </div>
                                </div>

                                <!-- Forgot Password / Register Links -->
                                <div class="form-group mb-0 row mt-2">
                                    <div class="col-sm-7 mt-3">
                                        <a href="{{ route('admin.password.reset') }}" class="text-muted"> <i class="mdi mdi-lock"></i> Forgot your password?</a>
                                    </div>
                                    
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-column flex-md-row text-center text-md-start justify-content-between mb-0 mt-1 py-4 px-4 px-xl-5  position-fixed w-100 bottom-0" style="background-color: {{ $brandColor }}; color: white;">
        <div class="text-white mb-3 mb-md-0">
            <script>document.write(new Date().getFullYear())</script> © {{ $brandName }}. All rights reserved.
        </div>
        <div>
            <!-- Add your content here -->
        </div>
    </div>
</section>
@endsection