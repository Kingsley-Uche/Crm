@extends('layouts.dashboard.landpage')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Locations</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('locations.index') }}">Locations</a></li>
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

        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="card-title">All Locations</h4>
                        <p class="card-title-desc">Locations of our properties</p>
                    </div>

                    <a href="{{ route('locations.create') }}" class="btn btn-success">
                        <i class="fa fa-plus text-white"></i> Add New Location
                    </a>
                </div>

                <div class="table-responsive">
                    <table id="selection-datatable" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>SN</th>
                                <th>Location Name</th>
                                <th>Branch</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($locations as $index => $location)
                                <tr>
                                    <td>{{ $index + 1 }}</td>

                                    <td>{{ ucwords($location->name) }}</td>

                                    <td>
                                        {{ optional($location->branch)->name ?? 'N/A' }}
                                    </td>

                                    <td>
                                        <a href="{{ route('locations.edit', $location->id) }}" class="btn btn-sm" title="Edit">
                                            <i class="fa fa-pen text-success"></i>
                                        </a>

                                        <form action="{{ route('locations.destroy', $location->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this location?');">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm" title="Delete">
                                                <i class="fas fa-trash-alt text-danger"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No locations found.</td>
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