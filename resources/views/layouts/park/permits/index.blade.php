@extends('layouts.dashboard.landpage')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Park Permits</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('park.permits.index') }}">Permits</a></li>
                    <li class="breadcrumb-item active">List</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="card-title">All Park Permits</h4>
                        <p class="card-title-desc">List of all park permits</p>
                    </div>
                    <a href="{{ route('park.permits.create') }}" class="btn btn-success">
                        <i class="fa fa-plus text-white"></i> Add New Permit
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>SN</th>
                                <th>Permit Name</th>
                                <th>Holder</th>
                                <th>Permit Number</th>
                                <th>Category</th>
                                <th>Created Date</th>
                                <th>Expiry Date</th>
                                <th>Fee</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($permits as $index => $permit)
                                <tr>
                                    <th scope="row">{{ $index + 1 }}</th>
                                    <td>{{ $permit->permit_name }}</td>
                                    <td>{{ $permit->fname }} {{ $permit->lname }}</td>
                                    <td>{{ $permit->uniqueId ?? 'N/A' }}</td>
                                    <td>{{ $permit->parkCategory->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($permit->start_time)->format('Y-m-d') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($permit->end_time)->format('Y-m-d') }}</td>
                                    <td>{{ number_format($permit->fee, 2) }}</td>
                                    <td>
                                        <a href="{{ route('park.permits.edit', $permit->id) }}" class="btn btn-sm" title="Edit">
                                            <i class="fa fa-pen text-success"></i>
                                        </a>
                                        <form action="{{ route('park.permits.destroy', $permit->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn delete-btn" title="Delete">
                                                <i class="fas fa-trash-alt text-danger"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No permits found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection