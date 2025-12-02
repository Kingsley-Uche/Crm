@extends('layouts.dashboard.landpage')

@section('content')
<style>
    th {
        font-size: 12px;
    }
    td {
        font-size: 12px;
    }
</style>

<!-- Success Message -->
<div class="row">
    <div class="col-12">
        <div class="card">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 px-1">Estate Owners</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Estate Owners</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Estate Owners Table -->
<div class="row">
    <div class="col-12">
        <div class="card px-1">
             <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="card-title">Estate Owner Records</span>
                <a href="{{ route('estate_owners.create') }}" class="btn btn-success">Create Estate Owner</a>
            </div>
            <p class="card-title-desc">View and manage Estate Owner information.</p>
             </div>
            <table id="datatable-buttons" class="table table` table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th>S/N</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Mobile Number</th>
                        <th>ID Method</th>
                        <th>Identification Image</th>
                        <th>Address</th>
                        <th>Next of Kin</th> 
                        <th>Next of Kin Phone</th>
                        <th>Bank Name</th>
                        <th>Account Number</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Modified Date</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($owners as $owner)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ ucwords($owner->fName) }}</td>
                            <td>{{ ucwords($owner->lName) }}</td>
                            <td>{{ $owner->email }}</td>
                            <td>{{ $owner->phones }}</td>
                            <td>{{ ucwords($owner->means_of_identification) }}</td>
                            <td>
                                @if ($owner->identification_image)
                                    <a href="{{ asset('storage/' . $owner->identification_image) }}" target="_blank">
                                        <i class="far fa-eye text-sm text-muted"></i>
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $owner->address }}</td>
                            <td>{{ ucwords($owner->next_of_kin) }}</td>
                            <td>{{ $owner->next_of_kin_phone }}</td>
                            <td>{{ ucwords($owner->bank_name) }}</td>
                            <td>{{ $owner->account_number }}</td>
                            <td>
                                <span class="badge {{ $owner->status == 'pending' ? 'badge-warning' : 'badge-success' }}">
                                    {{ ucfirst($owner->status) }}
                                </span>
                            </td>
                           <td>{{ \Carbon\Carbon::parse($owner->created_at)->timezone('Europe/London')->format('Y-m-d h:i:s A') }}</td>
                        <td>{{ \Carbon\Carbon::parse($owner->updated_at)->timezone('Europe/London')->format('Y-m-d h:i:s A') }}</td>

                            <td>
                                <a href="{{ route('estate_owners.edit', $owner->id) }}"><i class="fas fa-edit text-info" data-toggle="tooltip" title="Edit Owner"></i></a>

                                <form action="{{ route('estate_owners.destroy', $owner->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm delete-btn" 
                                            data-fname="{{ $owner->fName }}" 
                                            data-lname="{{ $owner->lName }}"  
                                            aria-label="Delete Owner">
                                        <i class="fas fa-trash-alt text-danger" data-toggle="tooltip" title="Delete owner"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination Links -->
            <div class="d-flex justify-content-end mt-3">
                {{ $owners->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

@endsection
