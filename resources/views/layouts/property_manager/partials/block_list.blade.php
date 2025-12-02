<div class="row" id="blocks-container">
    @foreach($blocks as $block)
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class='dripicons-home'></i> {{ $block->name }}  
                        <a href="{{ route('property.show', $block->id) }}" >
                            <i class='fas fa-pen-square text-success' data-toggle="tooltip" data-placement="top" title="Edit building"></i>
                        </a>
                    </h5>
                    <p class="card-text mb-1" style="line-height: 1.1;">
                        <strong>Landlord:</strong> {{ $block->landlord->fName ?? 'N/A' }} {{ $block->landlord->lName ?? '' }}<br>
                        <strong>State:</strong> {{ $block->state->name ?? 'N/A' }}<br>
                        <strong>LG:</strong> {{ $block->localGovernment->name ?? 'N/A' }}<br>
                        <strong>Address:</strong> {{ Str::limit($block->address, 100) }}
                    </p>
                    
                    @if($block->shelters->isNotEmpty())
                        @foreach ($block->shelters as $shelter)
                            @foreach ($cat as $category)
                                @if ($shelter->id === $category->id && $shelter->shelter_qty > 0)
                                    <p class="card-text mb-1" style="line-height: 1.1;">
                                        <strong>{{ ucwords($category->name) }}</strong> : {{ $shelter->shelter_qty }} units 
                                        <a href="{{ route('property.show', $shelter->id) }}" data-toggle="tooltip" data-placement="top" title="Edit Apartment(s)">
                                            <i class='fas fa-pen-square text-success'></i>
                                        </a>
                                    </p>
                                @endif
                            @endforeach
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
