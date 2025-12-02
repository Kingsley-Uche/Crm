<!doctype html>
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
<link rel="shortcut icon" href="{{ asset('assets/images/ctrlogo.jpg') }}">
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<style>
.bg-success,.btn-success {
    background-color: #77c44e !important;
    color: white; /* Optional: to ensure contrast */
}
    
</style>


<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-success shadow-sm">
            <div class="container-fluid">
                <div class="navbar-brand text-white text-center">
                   <strong> {{ config('app.name', 'CTR TRIANGLE TMO') }}</strong>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
