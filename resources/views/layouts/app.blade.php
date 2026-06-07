<!doctype html>
 @php
    $brand = session('brand_details') ?? cache('brand_details');

    $brandName = $brand['name'] ?? config('app.name');
    $brandColor = $brand['brand_color'] ?? '#074784';
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CTR TRIANGLE TMO') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
        <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{asset('assets/libs/twitter-bootstrap-wizard/prettify.css')}}">
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    
    <!-- App Css -->
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="{{ asset($brandLogo) }}">
    <!-- Scripts -->

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<style>
.bg-success,.btn-success {
    background-color:{{ $brandColor }} !important;
    color: white; /* Optional: to ensure contrast */
}
    
</style>


<body>
       
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light shadow-sm" style="background-color: {{ $brandColor }};">
            <div class="container-fluid">
                <div class="navbar-brand text-white text-center">
                   <strong> {{ $brandName }}</strong>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
