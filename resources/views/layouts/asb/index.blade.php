@extends('layouts.dashboard.landpage')

@section('styles')
    <link href="{{ asset('assets/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/datatables.net-select-bs4/css/select.bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<style>
    th, td {
        font-size: 12px;
    }
</style>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Anti-social Behaviour Records</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">ASB</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">ASB Reports</h4>
                <p class="card-title-desc">Review all anti-social behaviour records submitted.</p>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped dt-responsive nowrap w-100" id='selection-datatable'>
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Unit</th>
                                <th>Block</th>
                                <th>Issue</th>
                                <th>Description</th>
                                <th>Reporter Email</th>
                                <th>PC Ref</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Received Date</th>
                                <th>Appointment</th>
                                <th>Due Date</th>
                                <th>Completion Date</th>
                                <th>Document</th>
                                <th>Audio</th>
                                <th>Image</th>
                                 <th>Video</th>
                                
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($asb as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->unit_number }}</td>
                                    <td>{{ $item->block->name ?? 'N/A' }}</td>
                                    <td>{{ $item->issue }}</td>
                                    <td>{{ $item->description }}</td>
                                    <td>{{ $item->reporter_email }}</td>
                                    <td>{{ $item->crime_reference }}</td>
                                    <td>{{ $item->status }}</td>
                                    <td>{{ $item->assigned_to }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->received_date)->format('d M, Y h:i A') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->appointment)->format('d M, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->due_date)->format('d M, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->completion_date)->format('d M, Y') }}</td>
                                      <td>
                                        @if ($item->document)
                                          <i class="fa-solid fa-file"></i>
                                            
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->audio)
                                        <i class="fa-solid fa-file-audio"></i>
                                        
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->image)
                                         <i class="fa-solid fa-image"></i>
                                        
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->video)
                                         <i class="fa-solid fa-file-video"></i>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                     <td>
    <a href="{{ route('asb.edit', ['asb_id' => $item->id]) }}"><i class='fas fa-pencil-alt text-success'></i></a>
   <form action="{{ route('asb.destroy', ['asb_id' => $item->id]) }}" method="POST" class="d-inline">
    @csrf
    @method('DELETE')
 
 <button type="submit" class="btn btn-sm delete-btn" data-info="{{ $item-> crime_referemnce}}" aria-label="Delete Repairs">
        <i class="fas fa-trash-alt text-danger" data-toggle="tooltip" title="Delete Repair"></i>
    </button>
</form>

</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-select/js/dataTables.select.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.table').DataTable();
        });
    </script>
@endsection
