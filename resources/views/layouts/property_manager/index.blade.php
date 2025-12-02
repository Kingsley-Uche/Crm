@extends('layouts.dashboard.landpage')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Buildings or Blocks</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Registered</a></li>
                        <li class="breadcrumb-item active">Buildings or Blocks</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Search box with button --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="input-group">
                <input type="text" id="search" class="form-control" placeholder="Search buildings or blocks by name, state, landlord, etc...">
                <button id="search-btn" class="btn btn-success" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Blocks Container --}}
    <div class="row" id="blocks-container">
        @foreach($blocks as $block)
            <div class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="dripicons-home"></i> {{ucwords( $block->block_title) }}<br>
                            <i class="ri-function-line mr-1"></i>
                            <a href="{{ route('property.show', $block->block_model_id) }}">
                                <i class="ri-edit-fill" data-toggle="tooltip" title="Edit building"></i>
                            </a>

                            <form action="{{ route('property.destroy', $block->block_model_id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link p-0 m-0 delete-btn" aria-label="Delete a building">
                                    <i class="ri-delete-bin-5-line text-danger" data-toggle="tooltip" title="Delete a building"></i>
                                </button>
                            </form>
                        </h5>
                        <p class="card-text mb-1">
    <strong>Landlord:</strong> {{ $block->landlord->fName ?? 'N/A' }} {{ $block->landlord->lName ?? '' }}<br>
    <strong>Location:</strong> {{ ucfirst(optional($block->location)->name) ?? 'N/A' }}<br>
    <strong>Address:</strong> {{ \Illuminate\Support\Str::limit(ucfirst($block->block_address), 100) }}
</p>

                        @if($block->shelters->isNotEmpty())
                            @foreach($block->shelters as $shelter)
                            @if($shelter->shelter_qty>0)
                             
                                        <p class="card-text mb-1">
                                            <strong>{{ ucwords($shelter->shelter_name) }}:</strong> {{ $shelter->shelter_qty }} units
                                            <a href="{{ route('apartment.index', ['block_id' => $block->block_model_id, 'shelter_id' =>$shelter->shelter_id]) }}" class="ms-2" data-toggle="tooltip" title="Edit {{$shelter->shelter_name }}">
                                                <i class="fas fa-pen-square text-success"></i>
                                            </a>
                                        </p>
                                @endif()   
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-end mt-3">
        {{ $blocks->links('pagination::bootstrap-4') }}
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Tooltip initialization
            $('[data-toggle="tooltip"]').tooltip();

            // Confirm delete action
            // $('.delete-form').on('submit', function(e) {
            //     if (!confirm('Are you sure you want to delete this building?')) {
            //         e.preventDefault();
            //     }
            // });

            // Initialize Select2
            $('.select2').select2({
                placeholder: "Select an option",
                allowClear: true
            });

            // Handle search button click
            $('#search-btn').on('click', function() {
                let searchTerm = $('#search').val();
                // Perform AJAX search (You can implement it with Laravel AJAX or Livewire)
            });
        });
    </script>

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
    </style>
@endsection
