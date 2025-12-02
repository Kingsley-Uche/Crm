@extends('layouts.dashboard.landpage')


@section('content')
<style>
    th {
        font-size: 12px;
    },
    td{
        font-size: 12px; 
    }
</style>

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

  <!-- start page title -->
  <div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Occupants Data</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Occupants Data</a></li>
                    <li class="breadcrumb-item active">All</li>
                </ol>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="card px-1">
            <div class='card-body'>
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="card-title">Occupant Records</span>
            <a href="{{ route('occupant.create.form') }}" class="btn btn-success">Create Occupant</a>
        </div>
        <p class="card-title-desc">View and manage Occupant information.</p>
        </div>
<table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
    <thead class="table-light">
        <tr>
            <th>S/N</th>
            <th>First Name</th>
            <th>Middle Name</th>
            <th>Last Name</th>
            <th>Date of Birth</th>
            <th>Gender</th>
            <th>Nationality</th>
            <th>State</th>
            <th>Address</th>
            <th>ID Method</th>
            <th>Identification Image</th>
            <th>Passport Photograph</th>
            <th>Mobile Number</th>
            <th>Home Number</th>
            <th>Email</th>
            <th>Emergency Contact</th>
            <th>Emergency Email</th>
            <th>Next of Kin Full Name</th>
            <th>Next of Kin Address</th>
            <th>Next of Kin Email</th>
            <th>Next of Kin Phone</th>
            <!--<th>Guarantor Passport</th>-->
            <th>Created Date</th>
            <th>Modified Date</th>
            <th>Options</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($tenants as $tenant)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $tenant->first_name }}</td>
                <td>{{ $tenant->middle_name ?? 'N/A' }}</td>
                <td>{{ $tenant->last_name }}</td>
                <td>{{ $tenant->date_of_birth ? $tenant->date_of_birth->format('Y-m-d') : 'N/A' }}</td>
                <td>{{ $tenant->gender }}</td>
                <td>{{ $tenant->nationality }}</td>
                <td>{{ $tenant->state }}</td>
                <td>{{ $tenant->address }}</td>
                <td>{{ $tenant->id_method }}</td>
                <td>
                    @if ($tenant->identification_image)
                        <a href="{{ route('tenant.image', ['filename' => $tenant->identification_image]) }}" target="_blank">
                            <i class="far fa-eye text-muted"></i>
                        </a>
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    @if ($tenant->passport_photograph)
                        <a href="{{ route('tenant.image', ['filename' => $tenant->passport_photograph]) }}" target="_blank">
                            <i class="far fa-eye text-muted"></i>
                        </a>
                    @else
                        N/A
                    @endif
                </td>
                <td>{{ $tenant->mobile_number }}</td>
                <td>{{ $tenant->home_number ?? 'N/A' }}</td>
                <td>{{ $tenant->occupant_email ?? 'N/A' }}</td>
                <td>{{ $tenant->emergency_contact ?? 'N/A' }}</td>
                <td>{{ $tenant->emergency_email ?? 'N/A' }}</td>
                <td>{{ $tenant->guarantor_full_name ?? 'N/A' }}</td>
                <td>{{ $tenant->guarantor_address ?? 'N/A' }}</td>
                <td>{{ $tenant->guarantor_email ?? 'N/A' }}</td>
                <td>{{ $tenant->guarantor_phone ?? 'N/A' }}</td>
                <!--<td>-->
                <!--    @if ($tenant->guarantor_passport)-->
                <!--        <a href="{{ route('tenant.image', ['filename' => $tenant->guarantor_passport]) }}" target="_blank">-->
                <!--            <i class="far fa-eye text-muted"></i>-->
                <!--        </a>-->
                <!--    @else-->
                <!--        N/A-->
                <!--    @endif-->
                <!--</td>-->
                <td>{{ $tenant->created_at ? $tenant->created_at->timezone('Europe/London')->format('Y-m-d h:i:s A') : 'N/A' }}</td>
                <td>{{ $tenant->updated_at ? $tenant->updated_at->timezone('Europe/London')->format('Y-m-d h:i:s A') : 'N/A' }}</td>
                <td>
                    <a href="{{ route('occupants.edit.view', ['occupant_id' => $tenant->id]) }}">
                        <i class="fas fa-pencil-alt text-success"></i>
                    </a>
                    <form action="{{ route('occupant.destroy', $tenant->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete {{ $tenant->first_name }} {{ $tenant->last_name }}?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm delete-btn" aria-label="Delete Tenant">
                            <i class="fas fa-trash-alt text-danger" data-toggle="tooltip" title="Delete tenant"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>


                <!-- Pagination Links -->
                <div class="d-flex justify-content-end mt-3">
                    {{ $tenants->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>

                        

@endsection

