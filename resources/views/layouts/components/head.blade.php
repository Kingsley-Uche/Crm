<head>
  @php
    $brand = session('brand_details') ?? cache('brand_details');

    $brandName = $brand['name'] ?? config('app.name');
    $brandColor = $brand['brand_color'] ?? '#074784';
    $brand_description = $brand['description'] ?? 'CTR TRIANGLE TMO';
    $brand_logo = $brand['logo_url'] ?? asset('system_images/ctrlogo.png');
@endphp
    <meta charset="utf-8" />
    <title>@yield('title', $brandName)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $brand_description }}" />
    <meta name="author" content="Kamma Uche" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    

    <!-- Favicon -->
<!-- Favicon -->
<link rel="shortcut icon" href="{{ $brand_logo }}">

<!-- SweetAlert2 -->
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

<!-- DataTables Core + Extensions -->
<link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/datatables.net-select-bs4/css/select.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

<!-- Optional Wizard (if needed) -->
<link href="{{ asset('assets/libs/twitter-bootstrap-wizard/prettify.css') }}" rel="stylesheet" />

<!-- Theme CSS -->
<link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />

</head>
  
<style>
#sidebar-menu ul li a,
#sidebar-menu ul li a i,
#sidebar-menu ul li ul.sub-menu li a {
  color: #000000;
}

.topnav .navbar-nav .nav-link,
.topnav .navbar-nav .nav-link i,
.topnav .navbar-nav .dropdown-item {
  color: #000000;
}



.mm-active > a,
.mm-active > a i,
.mm-active > i,
.mm-active .active i,
.topnav .navbar-nav .nav-item .nav-link.active i,
body[data-sidebar=colored].vertical-collpsed .vertical-menu #sidebar-menu ul>li>a.mm-active i,
body[data-sidebar=dark].vertical-collpsed .vertical-menu #sidebar-menu ul>li>a.mm-active i,
.mm-active > a {
  color: {{ $brandColor }} !important;
}

.topnav .navbar-nav .nav-item .nav-link.active,
.topnav .navbar-nav .dropdown-item.active{
    color: {{ $brandColor }};
}

.bg-success,.btn-success {
    background-color: {{ $brandColor }} !important;
    color: white; /* Optional: to ensure contrast */
}

body {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
}

i {
    color: black !important;
}



</style>
