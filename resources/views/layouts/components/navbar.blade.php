<div class="vertical-menu mt-2">
    <div data-simplebar class="h-100">
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">Home</li>

                <li>
                    <a href="{{ route('admin.dashboard') }}" class="waves-effect"
                       style="{{ request()->routeIs('admin.dashboard') ? 'color: #074784 !important;' : '' }}">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                @php
$user = (Session::get('user'));
@endphp

                @if(Session::has('permissions'))
                    @if(Session::get('permissions')->contains('slug', 'create_estate_owner') || Session::get('permissions')->contains('slug', 'read_estate_owner'))
                        <li>
                            <a href="javascript:void(0);" class="waves-effect has-arrow"
                               style="{{ request()->routeIs('estate_owners.*') ? 'color: #074784 !important;' : '' }}">
                                <i class="fas fa-user-tie"></i>
                                <span>Estate Owner Manager</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                @if(Session::get('permissions')->contains('slug', 'create_estate_owner')||$user->is_system_admin===1)
                                    <li>
                                        <a href="{{ route('estate_owners.create') }}"
                                           style="{{ request()->routeIs('estate_owners.create') ? 'color: #074784 !important;' : '' }}">
                                            <i class="far fa-plus-square"></i> Create Owner
                                        </a>
                                    </li>
                                @endif
                                @if(Session::get('permissions')->contains('slug', 'read_estate_owner')|| $user->is_system_admin===1)
                                    <li>
                                        <a href="{{ route('estate_owners.index') }}"
                                           style="{{ request()->routeIs('estate_owners.index') ? 'color: #074784 !important;' : '' }}">
                                            <i class="far fa-eye"></i> View Owners
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                @endif

                @if(Session::has('permissions'))
                    @if(Session::get('permissions')->contains('slug', 'create_tenant') || Session::get('permissions')->contains('slug', 'read_tenant')||$user->is_system_admin===1)
                        <li>
                            <a href="javascript:void(0);" class="waves-effect has-arrow"
                               style="{{ request()->routeIs('occupant.*') ? 'color: #074784 !important;' : '' }}">
                                <i class="fas fa-users"></i>
                                <span>Occupants Manager</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                @if(Session::get('permissions')->contains('slug', 'create_tenant')|| $user->is_system_admin===1)
                                    <li>
                                        <a href="{{ route('occupant.create.form') }}"
                                           style="{{ request()->routeIs('occupant.create.form') ? 'color: #074784 !important;' : '' }}">
                                            <i class="far fa-plus-square"></i> Create Occupants
                                        </a>
                                    </li>
                                @endif
                                @if(Session::get('permissions')->contains('slug', 'read_tenant')||$user->is_system_admin===1)
                                    <li>
                                        <a href="{{ route('occupant.index') }}"
                                           style="{{ request()->routeIs('occupant.index') ? 'color: #074784 !important;' : '' }}">
                                            <i class="far fa-eye"></i> View Occupants
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                @endif

                @if(Session::has('permissions'))
                    @if(
                        Session::get('permissions')->contains('slug', 'create_locations') ||
                        Session::get('permissions')->contains('slug', 'read_locations') ||
                        Session::get('permissions')->contains('slug', 'create_property') ||
                        Session::get('permissions')->contains('slug', 'read_property') ||
                        Session::get('permissions')->contains('slug', 'read_apartments')||$user->is_system_admin===1
                    )
                        <li>
                            <a href="javascript:void(0);" class="has-arrow waves-effect"
                               style="{{ request()->routeIs('property.*') ? 'color: #074784 !important;' : '' }}">
                                <i class="fas fa-building"></i>
                                <span>Property Manager</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="true">
                            
                              
                                @if(Session::get('permissions')->contains('slug', 'create_locations') || Session::get('permissions')->contains('slug', 'read_locations')||$user->is_system_admin===1)
                                    <li>
                                        
                                        <a href="javascript:void(0);" class="has-arrow"
                                           style="{{ request()->routeIs('locations.create') || request()->routeIs('locations.index') ? 'color: #074784 !important;' : '' }}">
                                            <i class="fa fa-map-marker" aria-hidden="true"></i> Locations
                                        </a>
                                        <ul class="sub-menu" aria-expanded="true">
                                            @if(Session::get('permissions')->contains('slug', 'create_locations')||$user->is_system_admin===1)
                                                <li>
                                                    <a href="{{ route('locations.create') }}"
                                                       style="{{ request()->routeIs('locations.create') ? 'color: #074784 !important;' : '' }}">
                                                        <i class="fa fa-plus"></i> Create
                                                    </a>
                                                </li>
                                            @endif
                                            @if(Session::get('permissions')->contains('slug', 'read_locations')||$user->is_system_admin===1)
                                                <li>
                                                    <a href="{{ route('locations.index') }}"
                                                       style="{{ request()->routeIs('locations.index') ? 'color: #074784 !important;' : '' }}">
                                                        <i class="far fa-eye"></i> View
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </li>
                                @endif
                                @if(Session::get('permissions')->contains('slug', 'create_property') || Session::get('permissions')->contains('slug', 'read_property')||$user->is_system_admin===1)
                                    <li>
                                        <a href="javascript:void(0);" class="has-arrow"
                                           style="{{ request()->routeIs('property.create') || request()->routeIs('property.index')||$user->is_system_admin===1 ? 'color: #074784 !important;' : '' }}">
                                            <i class="fas fa-house-user"></i> Building or Blocks
                                        </a>
                                        <ul class="sub-menu" aria-expanded="true">
                                            @if(Session::get('permissions')->contains('slug', 'read_property')||$user->is_system_admin===1)
                                                <li>
                                                    <a href="{{ route('property.index') }}"
                                                       style="{{ request()->routeIs('property.index') ? 'color: #074784 !important;' : '' }}">
                                                        <i class="far fa-eye"></i> View Registered
                                                    </a>
                                                </li>
                                            @endif
                                            @if(Session::get('permissions')->contains('slug', 'create_property')||$user->is_system_admin===1)
                                                <li>
                                                    <a href="{{ route('property.create') }}"
                                                       style="{{ request()->routeIs('property.create') ? 'color: #074784 !important;' : '' }}">
                                                        <i class="far fa-plus-square"></i> Register a Block
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('property.import') }}"
                                                       style="{{ request()->routeIs('property.import') ? 'color: #074784 !important;' : '' }}">
                                                        <i class="fa fa-file-excel"></i> Import Properties
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </li>
                                @endif
                                @if(Session::get('permissions')->contains('slug', 'read_apartments')||$user->is_system_admin===1)
                                    <li>
                                        <a href="javascript:void(0);" class="has-arrow"
                                           style="{{ request()->routeIs('accommodation.index') || request()->routeIs('accommodation.booked') ? 'color: #074784 !important;' : '' }}">
                                            <i class="fas fa-bed"></i> Accommodations
                                        </a>
                                        <ul class="sub-menu" aria-expanded="true">
                                            <li>
                                                <a href="{{ route('accommodation.index') }}"
                                                   style="{{ request()->routeIs('accommodation.index') ? 'color: #074784 !important;' : '' }}">
                                                    <i class="far fa-eye"></i> View all
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('accommodation.booked') }}"
                                                   style="{{ request()->routeIs('accommodation.booked') ? 'color: #074784 !important;' : '' }}">
                                                    <i class="fa fa-check-square"></i> View Booked
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                @endif

                @if(Session::has('permissions'))
                    @if(Session::get('permissions')->contains('slug', 'create_tenancy') || Session::get('permissions')->contains('slug', 'read_tenancy')||$user->is_system_admin===1)
                        <li>
                            <a href="javascript:void(0)" class="waves-effect has-arrow">
                                <i class="fas fa-key"></i>
                                <span>Tenancy Manager</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="true">
                                @if(Session::get('permissions')->contains('slug', 'read_tenancy'))
                                    <li>
                                        <a href="{{ route('tenancy.index') }}"
                                           style="{{ request()->routeIs('tenancy.index') ? 'color: #074784 !important;' : '' }}">
                                            <i class="far fa-eye"></i> View all
                                        </a>
                                    </li>
                                @endif
                                @if(Session::get('permissions')->contains('slug', 'create_tenancy')||$user->is_system_admin===1)
                                    <li>
                                        <a href="{{ route('tenancy.show') }}"
                                           style="{{ request()->routeIs('tenancy.show') ? 'color: #074784 !important;' : '' }}">
                                            <i class="fa fa-plus"></i> Create Tenancy
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                @endif

                @if(Session::has('permissions'))
                    @if(Session::get('permissions')->contains('slug', 'read_rent') || Session::get('permissions')->contains('slug', 'create_rent')||$user->is_system_admin===1)
                        <li>
                            <a href="javascript:void(0)" class="waves-effect has-arrow">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <span>Rent Manager</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="true">
                                @if(Session::get('permissions')->contains('slug', 'read_rent')||$user->is_system_admin===1)
                                    <li>
                                        <a href="{{ route('apartments.view') }}"
                                           style="{{ request()->routeIs('apartments.view') ? 'color: #074784 !important;' : '' }}">
                                            <i class="fa fa-eye"></i> View Apartments
                                        </a>
                                    </li>
                                @endif
                                @if(Session::get('permissions')->contains('slug', 'create_rent')||$user->is_system_admin===1)
                                    <li>
                                        <a href="{{ route('rent.account') }}"
                                           style="{{ request()->routeIs('rent.account') ? 'color: #074784 !important;' : '' }}">
                                            <i class="fa fa-plus"></i> Create Rent Account
                                        </a>
                                    </li>
                                @endif
                                @if(Session::get('permissions')->contains('slug', 'read_rent')||$user->is_system_admin===1)
                                    <li>
                                        <a href="{{ route('rent.active') }}"
                                           style="{{ request()->routeIs('rent.active') ? 'color: #074784 !important;' : '' }}">
                                            <i class="fa fa-hotel"></i> Active Rent Accounts
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('rent.inactive') }}"
                                           style="{{ request()->routeIs('rent.inactive') ? 'color: #074784 !important;' : '' }}">
                                            <i class="fas fa-times-circle"></i> Deactivated Rent Accounts
                                        </a>
                                    </li>
                                @endif
                                @if(Session::get('permissions')->contains('slug', 'create_rent')||$user->is_system_admin===1)
                                    <li>
                                        <a href="{{ route('rent.cycle') }}"
                                           style="{{ request()->routeIs('rent.cycle') ? 'color: #074784 !important;' : '' }}">
                                            <i class="fa fa-clock"></i> Renew Rent
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                @endif

                @if(Session::has('permissions'))
                    @if(Session::get('permissions')->contains('slug', 'create_voids') || Session::get('permissions')->contains('slug', 'read_voids')||$user->is_system_admin===1)
                        <li>
                            <a href="javascript:void(0);" class="waves-effect has-arrow">
                                <i class="fas fa-ban"></i>
                                <span>Voids Manager</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="true">
                                @if(Session::get('permissions')->contains('slug', 'read_voids'))
                                    <li>
                                        <a href="{{ route('void.index') }}"
                                           style="{{ request()->routeIs('void.index') ? 'color: #074784 !important;' : '' }}">
                                            <i class="fa fa-eye"></i> View Voids
                                        </a>
                                    </li>
                                @endif
                                @if(Session::get('permissions')->contains('slug', 'create_voids'))
                                    <li>
                                        <a href="{{ route('void.create') }}"
                                           style="{{ request()->routeIs('void.create') ? 'color: #074784 !important;' : '' }}">
                                            <i class="fa fa-plus"></i> Create
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('void.import.load') }}"
                                           style="{{ request()->routeIs('void.import.load') ? 'color: #074784 !important;' : '' }}">
                                            <i class="fa fa-file-excel"></i> Import Voids
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                @endif

                @if(Session::has('permissions'))
                    @if(Session::get('permissions')->contains('slug', 'create_maintenance') || Session::get('permissions')->contains('slug', 'read_maintenance')||$user->is_system_admin===1)
                        <li>
                            <a href="javascript:void(0);" class="waves-effect has-arrow">
                                <i class="fas fa-tools"></i>
                                <span>Repairs</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="true">
                                @if(Session::get('permissions')->contains('slug', 'read_maintenance'))
                                    <li>
                                        <a href="{{ route('maintenance.index') }}"
                                           style="{{ request()->routeIs('maintenance.index') ? 'color: #074784 !important;' : '' }}">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                    </li>
                                @endif
                                @if(Session::get('permissions')->contains('slug', 'create_maintenance'))
                                    <li>
                                        <a href="{{ route('maintenance.create') }}"
                                           style="{{ request()->routeIs('maintenance.create') ? 'color: #074784 !important;' : '' }}">
                                            <i class="fa fa-plus"></i> Create Maintenance
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('maintenance.import') }}"
                                           style="{{ request()->routeIs('maintenance.import') ? 'color: #074784 !important;' : '' }}">
                                            <i class="fa fa-file-excel"></i> Import Maintenance
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                @endif
                
                  {{-- Complaints --}}
                @if(Session::has('permissions'))
    @if(Session::get('permissions')->contains('slug', 'create_complaints') || Session::get('permissions')->contains('slug', 'read_complaints')||$user->is_system_admin===1)
        <li>
            <a href="javascript:void(0);" class="waves-effect has-arrow">
                <i class="fas fa-bullhorn"></i>
                <span>Complaints Manager</span>
            </a>
            <ul class="sub-menu" aria-expanded="true">
                @if(Session::get('permissions')->contains('slug', 'read_complaints')||$user->is_system_admin===1)
                    <li>
                        <a href="{{ route('complaints.index') }}"
                           style="{{ request()->routeIs('complaints.index') ? 'color: #074784 !important;' : '' }}">
                            <i class="fa fa-eye"></i>Complaints
                        </a>
                    </li>
                @endif
                @if(Session::get('permissions')->contains('slug', 'create_complainst')||$user->is_system_admin===1)
                    <li>
                        <a href="{{ route('complaints.create') }}"
                           style="{{ request()->routeIs('complaints.create') ? 'color: #074784 !important;' : '' }}">
                            <i class="fa fa-plus"></i> Create
                        </a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
@endif
                {{-- CAR PARK --}}
@if(Session::has('permissions'))
    @if(Session::get('permissions')->contains('slug', 'create_park') || Session::get('permissions')->contains('slug', 'read_park')||$user->is_system_admin===1)
        <li>
            <a href="javascript:void(0);" class="waves-effect has-arrow">
                <i class="fas fa-parking"></i>
                <span>Car Park</span>
            </a>
            <ul class="sub-menu" aria-expanded="false">
                {{-- Park Categories --}}
                <li>
                    <a href="javascript:void(0);" class="has-arrow">
                        <i class="fas fa-list-alt me-1"></i> Park Categories
                    </a>
                    <ul class="sub-menu">
                        @if(Session::get('permissions')->contains('slug', 'create_park')||$user->is_system_admin===1)
                            <li>
                                <a href="{{ route('park.categories.create') }}"
                                   style="{{ request()->routeIs('park.categories.create') ? 'color: #074784 !important;' : '' }}">
                                    <i class="fas fa-plus me-1"></i> Create
                                </a>
                            </li>
                        @endif
                        @if(Session::get('permissions')->contains('slug', 'read_park')||$user->is_system_admin===1)
                            <li>
                                <a href="{{ route('park.categories.index') }}"
                                   style="{{ request()->routeIs('park.categories.index') ? 'color: #074784 !important;' : '' }}">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
                {{-- Park Locations --}}
                <li>
                    <a href="javascript:void(0);" class="has-arrow">
                        <i class="fas fa-map-marker-alt me-1"></i> Park Locations
                    </a>
                    <ul class="sub-menu">
                        @if(Session::get('permissions')->contains('slug', 'create_park')||$user->is_system_admin===1)
                            <li>
                                <a href="{{ route('park.models.create') }}"
                                   style="{{ request()->routeIs('park.models.create') ? 'color: #074784 !important;' : '' }}">
                                    <i class="fas fa-plus me-1"></i> Create
                                </a>
                            </li>
                        @endif
                        @if(Session::get('permissions')->contains('slug', 'read_park')||$user->is_system_admin===1)
                            <li>
                                <a href="{{ route('park.models.index') }}"
                                   style="{{ request()->routeIs('park.models.index') ? 'color: #074784 !important;' : '' }}">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
                {{-- Park Taxes --}}
                <li>
                    <a href="javascript:void(0);" class="has-arrow">
                        <i class="fas fa-file-invoice-dollar me-1"></i> Park Taxes
                    </a>
                    <ul class="sub-menu">
                        @if(Session::get('permissions')->contains('slug', 'create_park')||$user->is_system_admin===1)
                            <li>
                                <a href="{{ route('park.taxes.create') }}"
                                   style="{{ request()->routeIs('park.taxes.create') ? 'color: #074784 !important;' : '' }}">
                                    <i class="fas fa-plus me-1"></i> Create
                                </a>
                            </li>
                        @endif
                        @if(Session::get('permissions')->contains('slug', 'read_park')||$user->is_system_admin===1)
                            <li>
                                <a href="{{ route('park.taxes.index') }}"
                                   style="{{ request()->routeIs('park.taxes.index') ? 'color: #074784 !important;' : '' }}">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
                {{-- Park Permits --}}
                <li>
                    <a href="javascript:void(0);" class="has-arrow">
                        <i class="fas fa-id-badge me-1"></i> Park Permits
                    </a>
                    <ul class="sub-menu">
                        @if(Session::get('permissions')->contains('slug', 'create_park')||$user->is_system_admin===1)
                            <li>
                                <a href="{{ route('park.permits.create') }}"
                                   style="{{ request()->routeIs('park.permits.create') ? 'color: #074784 !important;' : '' }}">
                                    <i class="fas fa-plus me-1"></i> Create
                                </a>
                            </li>
                        @endif
                        @if(Session::get('permissions')->contains('slug', 'read_park')||$user->is_system_admin===1)
                            <li>
                                <a href="{{ route('park.permits.index') }}"
                                   style="{{ request()->routeIs('park.permits.index') ? 'color: #074784 !important;' : '' }}">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
                         <li>
                    <a href="javascript:void(0);" class="has-arrow">
                        <i class="fas fa-car me-1"></i> Parking
                    </a>
                    <ul class="sub-menu">
                        @if(Session::get('permissions')->contains('slug', 'create_park')||$user->is_system_admin===1)
                            <li>
                                <a href="{{ route('parking.bound') }}"
                                   style="{{ request()->routeIs('parking.bound') ? 'color: #074784 !important;' : '' }}">
                                    <i class="fas fa-arrow-left"></i><i class="fas fa-arrow-right"></i>In / Out                                </a>
                            </li>
                        @endif
                        @if(Session::get('permissions')->contains('slug', 'read_park')||$user->is_system_admin===1)
                             <li>
                                <a href="{{ route('parking.status') }}"
                                   style="{{ request()->routeIs('parking.status') ? 'color: #074784 !important;' : '' }}">
                                    <i class="fas fa-info-circle"></i>Status
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            </ul>
        </li>
    @endif
@endif

{{-- ASB --}}
@if(Session::has('permissions'))
    @if(Session::get('permissions')->contains('slug', 'create_asb') || Session::get('permissions')->contains('slug', 'read_asb')||$user->is_system_admin===1)
        <li>
            <a href="javascript:void(0);" class="waves-effect has-arrow">
                <i class="fa fa-user-slash"></i>
                <span>ASB</span>
            </a>
            <ul class="sub-menu" aria-expanded="true">
                @if(Session::get('permissions')->contains('slug', 'create_asb')||$user->is_system_admin===1)
                    <li>
                        <a href="{{ route('asb.create') }}"
                           style="{{ request()->routeIs('asb.create') ? 'color: #074784 !important;' : '' }}">
                            <i class="fa fa-plus"></i> Create ASB
                        </a>
                    </li>
                @endif
                @if(Session::get('permissions')->contains('slug', 'read_asb'))
                    <li>
                        <a href="{{ route('asb.index') }}"
                           style="{{ request()->routeIs('asb.index') ? 'color: #074784 !important;' : '' }}">
                            <i class="fa fa-eye"></i> View ASB
                        </a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
@endif

{{-- FOB Manager --}}
@if(Session::has('permissions'))
    @if(Session::get('permissions')->contains('slug', 'create_fob') || Session::get('permissions')->contains('slug', 'read_fob')||$user->is_system_admin===1)
        <li>
            <a href="javascript:void(0);" class="waves-effect has-arrow">
                <i class="fas fa-lock"></i>
                <span>FOB Manager</span>
            </a>
            <ul class="sub-menu" aria-expanded="true">
                @if(Session::get('permissions')->contains('slug', 'read_fob'))
                    <li>
                        <a href="{{ route('fobs.index') }}"
                           style="{{ request()->routeIs('fobs.index') ? 'color: #074784 !important;' : '' }}">
                            <i class="fa fa-eye"></i> FOBs
                        </a>
                    </li>
                @endif
                @if(Session::get('permissions')->contains('slug', 'create_fob'))
                    <li>
                        <a href="{{ route('fobs.create') }}"
                           style="{{ request()->routeIs('fobs.create') ? 'color: #074784 !important;' : '' }}">
                            <i class="fa fa-plus"></i> Create
                        </a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
@endif


{{-- REPORTS --}}
@if(Session::has('permissions'))
    @if(Session::get('permissions')->contains('slug', 'read_reports')||$user->is_system_admin===1)
        <li>
            <a href="javascript:void(0);" class="waves-effect has-arrow">
                <i class="fas fa-chart-line"></i>
                <span>Reports</span>
            </a>
            <ul class="sub-menu" aria-expanded="true">
                <li>
                    <a href="{{ route('rent.report') }}"
                       style="{{ request()->routeIs('rent.report') ? 'color: #074784 !important;' : '' }}">
                        <i class="fas fa-file-invoice-dollar"></i> Rent Report
                    </a>
                </li>
                <li>
                    <a href="{{ route('pest_control.report') }}"
                       style="{{ request()->routeIs('pest_control.report') ? 'color: #074784 !important;' : '' }}">
                        <i class="fa fa-bug"></i> Pest Control Report
                    </a>
                </li>
                <li>
                    <a href="{{ route('maintenance.report') }}"
                       style="{{ request()->routeIs('maintenance.report') ? 'color: #074784 !important;' : '' }}">
                        <i class="fa fa-tools"></i> Maintenance Report
                    </a>
                </li>
                <li>
                    <a href="{{ route('voids.report') }}"
                       style="{{ request()->routeIs('voids.report') ? 'color: #074784 !important;' : '' }}">
                        <i class="fas fa-ban"></i> Voids Report
                    </a>
                </li>
                <li>
                    <a href="{{ route('complaints.report') }}"
                       style="{{ request()->routeIs('complaints.report') ? 'color: #074784 !important;' : '' }}">
                       <i class="fas fa-bullhorn"></i>Complaints Report
                    </a>
                </li>

            </ul>
        </li>
    @endif
@endif

@if($user->is_system_admin===1)
<li>
    <a href="javascript:void(0);" class="waves-effect has-arrow"
       style="{{ request()->routeIs('access.users.*') || request()->routeIs('access.roles.*') || request()->routeIs('access.assign.*') ? 'color: #074784 !important;' : '' }}">
        <i class="fas fa-user-shield"></i>
        <span>Access Control</span> 
    </a>
    <ul class="sub-menu" aria-expanded="false">
        {{-- User Management --}}
        <li>
            <a href="{{ route('access.users.index') }}"
               style="{{ request()->routeIs('access.users.*') ? 'color: #074784 !important;' : '' }}">
               <i class="fas fa-user-friends"></i> User Management
            </a>
        </li>

        {{-- Role Management --}}
        <li>
            <a href="{{ route('access.roles.index') }}"
               style="{{ request()->routeIs('access.roles.*') ? 'color: #074784 !important;' : '' }}">
               <i class="fas fa-user-tag"></i> Role Management
            </a>
        </li>

            </ul>
</li>

@endif
@if($user->is_site_admin == 1)
<li>
    <a href="javascript:void(0);" class="waves-effect has-arrow"
       style="{{ request()->routeIs('subscriptions.*') || request()->routeIs('subscription.account.*') ? 'color: #074784 !important;' : '' }}">
        <i class="fas fa-user-shield"></i>
        <span>Subscription Manager</span>
    </a>

    <ul class="sub-menu" aria-expanded="false">

        {{-- Subscription Plans --}}
        <li>
            <a href="{{ route('subscriptions.index') }}"
               style="{{ request()->routeIs('subscriptions.*') ? 'color: #074784 !important;' : '' }}">
                <i class="fas fa-user-friends"></i>Subscription Plans
            </a>
        </li>

        {{-- Subscription Account --}}
        <li>
            <a href="{{ route('subscription.account.index') }}"
               style="{{ request()->routeIs('subscription.account.*') ? 'color: #074784 !important;' : '' }}">
                <i class="fas fa-user-tag"></i> Subscription Account
            </a>
        </li>

    </ul>
</li>
@endif

@if($user->is_site_admin == 1)
<li>
    <a href="javascript:void(0);" class="waves-effect has-arrow"
       style="{{ request()->routeIs('brand.*')? 'color: #074784 !important;' : '' }}">
        <i class="fas fa-user-shield"></i>
        <span>Brand Management</span>
    </a>

    <ul class="sub-menu" aria-expanded="false">

        {{-- Brand Details --}}
        <li>
            <a href="{{ route('brand.index') }}"
               style="{{ request()->routeIs('brand.*') ? 'color: #074784 !important;' : '' }}">
                <i class="fas fa-user-friends"></i>Brand Details
            </a>
        </li>


    </ul>
</li>
@endif


            </ul>
        </div>
    </div>
</div>


