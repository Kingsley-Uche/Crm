@extends('layouts.dashboard.landpage')

@section('content')
 <link href="{{ asset('assets/libs/dropzone/min/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
 <!-- Custom Green Preloader -->

    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Import Properties</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Property Profile Manager</a></li>
                        <li class="breadcrumb-item active">Import Spreadsheet</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
     <div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Property Import</h4>
                <p class="card-title-desc">This supports only spreadsheet files</p>

                <div>
                    <form action="{{ route('property.import.upload') }}" class="dropzone" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="fallback">
                            <input name="file" type="file" multiple="multiple">
                        </div>
                        <div class="dz-message needsclick">
                            <div class="mb-3">
                                <i class="display-4 text-muted ri-upload-cloud-2-line"></i>
                            </div>
                            <h4>Drop files here or click to upload.</h4>
                            
                        </div>
                        
                    </form>
                </div>

            </div> 
        </div>
    </div> <!-- end col -->
</div> <!-- end row -->








@endsection

@section('scripts')
<script src="{{ asset('assets/libs/dropzone/min/dropzone.min.js') }}"></script>
@endsection