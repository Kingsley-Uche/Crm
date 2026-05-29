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
            <h4 class="mb-sm-0">Subscription Plans</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Subscriptions</a></li>
                    <li class="breadcrumb-item active">Plans</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="card-title">Subscription Plans</h4>
                        <p class="card-title-desc">Manage all subscription plans and pricing tiers.</p>
                    </div>

                    <a href="{{ route('subscriptions.create') }}" class="btn btn-success btn-sm">
                        + Create Plan
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped dt-responsive nowrap w-100" id="selection-datatable">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Admins</th>
                                <th>Branches</th>
                                <th>Apartments</th>
                                <th>Managers</th>
                                <th>Discount %</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Options</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($plans as $index => $plan)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $plan->name }}</td>
                                    <td>₦{{ number_format($plan->price_per_month, 2) }}</td>
                                    <td>{{ $plan->number_admins }}</td>
                                    <td>{{ $plan->number_branches }}</td>
                                    <td>{{ $plan->number_apartments }}</td>
                                    <td>{{ $plan->number_property_managers }}</td>
                                    <td>{{ $plan->discount_percentage ?? 0 }}%</td>

                                    <td>
                                        @if($plan->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $plan->created_at ? \Carbon\Carbon::parse($plan->created_at)->format('d M, Y') : 'N/A' }}
                                    </td>

                                    <td>
                                        <a href="{{ route('subscriptions.edit', $plan->id) }}">
                                            <i class="fas fa-pencil-alt text-success"></i>
                                        </a>

                                        <form action="{{ route('subscriptions.destroy', $plan->id) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm delete-btn"
                                                    data-info="{{ $plan->name }}"
                                                    aria-label="Delete Plan">
                                                <i class="fas fa-trash-alt text-danger"></i>
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
        $(document).ready(function () {
            $('#selection-datatable').DataTable();
        });
    </script>
@endsection