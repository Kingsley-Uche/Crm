@extends('layouts.dashboard.landpage')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Building or Blocks</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Registered</a></li>
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
            <button id="search-btn" class="btn  btn-success fas fa-search" type="button"></button>
        </div>
    </div>
</div>

{{-- Blocks container (will be updated dynamically) --}}
<div class="row" id="blocks-container">
    @foreach($blocks as $block)
        <div class="col-md-3 mb-4">
            <div class="card card-body">
              
                <h3 class="card-title">  <i class='dripicons-home'></i> : {{ $block->name }}</h3>
                <p class="card-text">
                    Landlord: {{ $block->landlord->fName. ' '.$block->landlord->lName ?? 'N/A' }} <br>
                    State: {{ $block->state->name ?? 'N/A' }} <br>
                    LG: {{ $block->localGovernment->name ?? 'N/A' }} <br>
                    Address: {{ Str::limit($block->address, 100) }}
                </p>
                <a href="{{ route('property.show', $block->id) }}" class="btn btn-success waves-effect waves-light">View Apartments</a>
            </div>
        </div>
    @endforeach
</div>

<div class="d-flex justify-content-end mt-3">
    {{ $blocks->links('pagination::bootstrap-4') }}
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Trigger search on Enter keypress or when search button is clicked
        $('#search').on('keypress', function(e) {
            if (e.which == 13) { // Enter key
                triggerSearch();
            }
        });

        $('#search-btn').on('click', function() {
            triggerSearch();
        });

        function triggerSearch() {
            var query = $('#search').val().trim(); // Get the search query
            
            // If search query is empty, no need to trigger a search
            if (!query) {
                alert("Please enter a search term.");
                return;
            }

            // Make AJAX request to fetch filtered and sorted blocks
            $.ajax({
                url: "", // Define the route for search
                type: 'GET',
                data: { query: query },
                success: function(data) {
                    // Update the blocks container with the new filtered data
                    $('#blocks-container').html(data);
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }
    });
</script>
@endsection
