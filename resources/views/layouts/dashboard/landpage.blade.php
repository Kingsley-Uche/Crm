@include('layouts.components.head')
 @php
    $brand = session('brand_details') ?? cache('brand_details');

    $brandName = $brand['name'] ?? config('app.name');
    $brandColor = $brand['brand_color'] ?? '#074784';
    $brand_description = $brand['description'] ?? 'CTR TRIANGLE TMO';
@endphp
  <div id="preloader">
            <div id="status">
                <div class="spinner">
    <i class="ri-loader-line spin-icon" style="font-size: 1rem; color: {{ $brandColor }};">
        {{ substr($brandName, 0, 12) }}
    </i>
</div>
            </div>
        </div>
<div id="layout-wrapper">
    @include('layouts.components.header')
    @include('layouts.components.navbar')
    
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
        <!-- End Page-content -->

        <footer class="footer" style="background-color: {{ $brandColor }};">
            <div class="container-fluid">
                <div class="row text-white">
                    <div class="col-sm-6">
                        <script>document.write(new Date().getFullYear())</script> © {{ $brandName }}.
                    </div>
                    <div class="col-sm-6">

                    </div>
                </div>
            </div>
        </footer>
    </div>
    <!-- End main-content -->
</div>
<!-- End layout-wrapper -->

@include('layouts.components.rightbar')
@include('layouts.components.footer')

<!-- Yield scripts from content pages -->
@yield('scripts')