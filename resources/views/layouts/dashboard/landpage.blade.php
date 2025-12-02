@include('layouts.components.head')
  <div id="preloader">
            <div id="status">
                <div class="spinner">
    <i class="ri-loader-line spin-icon text-success" style="font-size: 1rem;">CTR LTD</i>
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

        <footer class="footer bg-success">
            <div class="container-fluid">
                <div class="row text-white">
                    <div class="col-sm-6">
                        <script>document.write(new Date().getFullYear())</script> © CTR TRIANGLE TMO.
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