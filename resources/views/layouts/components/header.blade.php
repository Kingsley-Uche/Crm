 


<style>
    .pagination .page-item .page-link {
        color: black;
        border-color: #28a745;
    }

    .pagination .page-item.active .page-link {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
        color: #fff;
    }

    .pagination .page-item .page-link:hover {
        background-color: #218838;
        border-color: #218838;
        color: #fff;
    }

    .navbar-brand-box {
        padding: 10px 15px;
        background-color: #fff;
        display: flex;
        align-items: center;
        border-radius: 5px;
    }

    .logo-sm img {
        height: 35px;
    }

    .logo-lg img {
        height:100px;
    }

    .logo-lg span.text-black {
        display: none; /* Hide text if using only image */
    }
</style>

 <header id="page-topbar" class='bg-success'>
                <div class="navbar-header mt-2">
<div class="d-flex">
    <!-- LOGO -->
<div class="navbar-brand-box d-flex justify-content-center align-items-center p-0 m-0">
    <a href="{{ route('admin.dashboard') }}" class="logo logo-white text-center p-0 m-0">
        <span class="logo-sm p-0 m-0">
            <img src="{{ url('system_images/ctrlogo.jpg') }}" class="img-fluid p-0 m-0" alt="Tenant Management Organization">
        </span>
        <span class="logo-lg p-0 m-0">
            <img src="{{ url('system_images/ctrlo.jpg') }}" class="img-fluid p-0 m-0" alt="Tenant Management Organization">
        </span>
    </a>
</div>


    <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect" id="vertical-menu-btn">
        <i class="ri-menu-2-line align-middle text-white"></i>
    </button>

   

 
</div>


<div class="d-flex">

<div class="dropdown d-inline-block d-lg-none ms-2">
    <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-search-dropdown"
        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="ri-search-line text-white"></i>
    </button>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
        aria-labelledby="page-header-search-dropdown">
        <form class="p-3">
            <div class="mb-3 m-0">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search ...">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="ri-search-line"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>




<div class="dropdown d-none d-lg-inline-block ms-1">
    <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
        <i class="ri-fullscreen-line text-white"></i>
    </button>
</div>

<div class="dropdown d-inline-block">
    <button type="button" class="btn header-item noti-icon waves-effect"
        id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="ri-notification-3-line text-white"></i>
        <span class="noti-dot"></span>
    </button>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
        aria-labelledby="page-header-notifications-dropdown">
        <div class="p-3">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0"> Notifications </h6>
                </div>
                <div class="col-auto">
                    <a href="#!" class="small"> View All</a>
                </div>
            </div>
        </div>
       <div data-simplebar style="max-height: 230px;">
    {{-- Repairs --}}
    @foreach($notifications['repairs'] as $repair)
        <a href="#" class="text-reset notification-item">
            <div class="d-flex">
                <div class="avatar-xs me-3">
                    <span class="avatar-title bg-danger rounded-circle font-size-16">
                        <i class="fas fa-tools text-white"></i>
                    </span>
                </div>
                <div class="flex-1">
                    <h6 class="mb-1">Repair: {{ $repair->repair_type }}</h6>
                    <div class="font-size-12 text-muted">
                        <p class="mb-1">Block: {{ ucwords($repair->block->name) ?? 'N/A' }} | Unit: {{ $repair->unit_number }}</p>
                        <p class="mb-0">
                            <i class="fas fa-calendar-alt"></i>
                            {{ \Carbon\Carbon::parse($repair->appointment)->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>
        </a>
    @endforeach

    {{-- Park Permits --}}
    @foreach($notifications['park_permits'] as $permit)
        <a href="#" class="text-reset notification-item">
            <div class="d-flex">
                <div class="avatar-xs me-3">
                    <span class="avatar-title bg-warning rounded-circle font-size-16">
                        <i class="fas fa-parking text-white"></i>
                    </span>
                </div>
                <div class="flex-1">
                    <h6 class="mb-1">{{ ucwords($permit->fname) }} {{ucwords( $permit->lname) }}</h6>
                    <div class="font-size-12 text-muted">
                        <p class="mb-1">Permit: {{ $permit->permit_name }}</p>
                        <p class="mb-0">
                            <i class="fas fa-calendar-times"></i>
                            Expires {{ \Carbon\Carbon::parse($permit->end_time)->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>
        </a>
    @endforeach

    {{-- Pest Control --}}
    @foreach($notifications['pest_control'] as $pest)
        <a href="#" class="text-reset notification-item">
            <div class="d-flex">
                <div class="avatar-xs me-3">
                    <span class="avatar-title bg-success rounded-circle font-size-16">
                        <i class="fas fa-bug text-white"></i>
                    </span>
                </div>
                <div class="flex-1">
                    <h6 class="mb-1">Pest: {{ $pest->issue_type }}</h6>
                    <div class="font-size-12 text-muted">
                        <p class="mb-1">Block: {{ ucwords($pest->block->name) ?? 'N/A' }}</p>
                        <p class="mb-0">
                            <i class="fas fa-clock"></i>
                            Appointment {{ \Carbon\Carbon::parse($pest->appointment)->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>
        </a>
    @endforeach
</div>

        <div class="p-2 border-top">
            <div class="d-grid">
                <a class="btn btn-sm btn-link font-size-14 text-center" href="javascript:void(0)">
                    <i class="mdi mdi-arrow-right-circle me-1"></i> View More..
                </a>
            </div>
        </div>
    </div>
</div>

<div class="dropdown d-inline-block user-dropdown">
    <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class='fa fa-user text-white'></i>
        <span class="d-none d-xl-inline-block ms-1 text-white">
            {{ $user_logged_in->fName . ' ' . $user_logged_in->lName }}
</span>
        <i class="mdi mdi-chevron-down d-none d-xl-inline-block text-white"></i>
    </button>
    <div class="dropdown-menu dropdown-menu-end">
      
        <div class="dropdown-divider"></div>
        <a class="dropdown-item text-danger" href="{{ route('admin.logout')}}"><i
                class="ri-shut-down-line align-middle me-1 text-danger"></i> Logout</a>
    </div>
</div>

</div>

                </div>
                @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

            </header>