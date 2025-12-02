@extends('layouts.dashboard.landpage')

@section('content')
    <!-- Custom Preloader for AJAX -->
    <div id="ajax-preloader">
        <div class="spinner">
            <i class="ri-loader-line"></i>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Accommodations</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Accommodations</a></li>
                        <li class="breadcrumb-item active">All</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        @foreach($accom as $acc)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title text-black mb-3">
                            <i class="fas fa-home me-2"></i>{{ ucfirst($acc['name']) }}
                        </h4>
                       <p class="card-text text-muted">
                            <i class="fas fa-bed me-2"></i>Total: 
                            <span class="badge bg-success">{{ $acc['qty'] }}</span>&nbsp;&nbsp;
                            @if(!empty($acc['booked']))
                                <span class="text-info"> &#9679;</span>Available: <span class="badge bg-warning">{{ $acc['qty'] - $acc['booked'] }}</span> <br>
                                <span class="text-danger"> &#9679;</span><i class="fa fa-calendar-check me-1 text-danger" aria-hidden="true"></i>Booked: <span class="badge bg-danger"> {{ $acc['booked'] }}</span>
                            @else
                                <span class="text-info"> &#9679;</span>Available:</span><span class="badge bg-warning">{{$acc['qty'] }} </span><br>
                                <span class="text-danger"> &#9679;</span><i class="fa fa-calendar-check me-1" aria-hidden="true"></i>Booked: <span class="badge bg-danger"> 0</span>
                            @endif
                        </p>

                        <div id="accordion-{{ $acc['shelter_id'] }}" class="accordion">
                            <div class="accordion-item border-0">
                                <h2 class="accordion-header" id="heading-{{ $acc['shelter_id'] }}">
                                    <button class="accordion-button collapsed bg-white" 
                                            type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#collapse-{{ $acc['shelter_id'] }}"
                                            aria-expanded="false" 
                                            aria-controls="collapse-{{ $acc['shelter_id'] }}">
                                        <i class="fas fa-eye me-2"></i> View Details
                                    </button>
                                </h2>
                                
                                <div id="collapse-{{ $acc['shelter_id'] }}" 
                                     class="accordion-collapse collapse" 
                                     aria-labelledby="heading-{{ $acc['shelter_id'] }}"
                                     data-bs-parent="#accordion-{{ $acc['shelter_id'] }}">
                                    <div class="accordion-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-striped mb-0">
                                                <tbody>
                                                    @foreach($acc['blocks'] as $block)
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex flex-column small-text">
                                                                    <span class="mb-1">
                                                                        <i class="fas fa-building me-2 text-muted"></i>
                                                                        <strong>Block :</strong> {{ ucwords($block['block_name']) }}
                                                                    </span>
                                                                    <span class="mb-1">
                                                                        <i class="fas fa-home me-2 text-muted"></i>
                                                                        <strong>Shelter:</strong> {{ ucfirst($block['shelter_name']) }}
                                                                    </span>
                                                                    <span class="mb-1">
                                                                        <i class="ri-numbers-line"></i></i>
                                                                        <span class="badge bg-warning"> {{ $block['shelter_qty'] }} </span>
                                                                           <i class=" far fa-calendar-check me-1" aria-hidden="true"></i>
                                                                         <span class="badge bg-danger"> {{ $block['booked_count'] }}</span>
                                                                    </span>
                                                                    @if($block['location'])
                                                                        <span class="mb-1">
                                                                            <i class="fas fa-map-marker-alt me-2 text-muted"></i>
                                                                            <strong>Location:</strong> {{ ucwords($block['location']['name']) }}
                                                                        </span>
                                                                    @endif
                                                                    <span class="mb-1">
                                                                        <i class="fa fa-address-card me-2 text-muted"></i>
                                                                        <strong>Address:</strong> {{ucwords( $block['address']) }}
                                                                    </span>
                                                                    @if($block['landlord_details'])
                                                                        <span class="mb-1">
                                                                            <i class="fas fa-user me-2 text-muted"></i>
                                                                            <strong>Landlord:</strong> 
                                                                            {{ $block['landlord_details']['fName'] }} 
                                                                            {{ $block['landlord_details']['lName'] }}
                                                                        </span>
                                                                        <span class="mb-1">
                                                                            <i class="fas fa-envelope me-2 text-muted"></i>
                                                                            <strong>Email:</strong> {{ $block['landlord_details']['email'] }}
                                                                        </span>
                                                                        <span>
                                                                            <i class="fas fa-phone me-2 text-muted"></i>
                                                                            <strong>Phone:</strong> {{ $block['landlord_details']['phones'] }}
                                                                        </span>
                                                                      <div class="d-flex justify-content-end"><a class="btn btn-success btn-sm fas fa-external-link-alt"
                                                                      href="{{ route('accommodation.block', ['block_id' => $block['block_id'], 'shelter_id' => $acc['shelter_id']]) }}" 
                                                                      data-bs-toggle="tooltip" title="Visit this"></a></div>
                                                                    @endif
                                                                </div>
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
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@section('scripts')
    <style>
        #ajax-preloader {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(40, 167, 69, 0.8);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
            transition: opacity 0.5s ease;
        }

        .spinner {
            font-size: 36px;
            color: white;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .table td {
            border: none;
            padding: 0.75rem; /* Reduced padding to match smaller text */
        }

        .small-text {
            font-size: 0.875rem; /* Approximately 14px, smaller than default */
        }

        .small-text strong {
            font-weight: 700; /* Ensures bold remains strong */
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const collapseLinks = document.querySelectorAll('.accordion-button');
            collapseLinks.forEach(link => {
                const icon = link.querySelector('i');
                
                link.addEventListener('click', function() {
                    if (this.classList.contains('collapsed')) {
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    } else {
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    }
                });
            });
        });
    </script>
@endsection