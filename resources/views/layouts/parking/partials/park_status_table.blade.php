<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Parking Status Report</h4>
                <p class="card-title-desc">Click any of the buttons to export to a desired format.</p>

                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap"
                       style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Permit Code</th>
                            <th>Inbound Time</th>
                            <th>Outbound Time</th>
                            <th>Park Duration</th>
                            <th>Captured By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $index => $track)
                            @php
                                $inbound = \Carbon\Carbon::parse($track->inbound_time);
                                $outbound = $track->outbound_time 
                                    ? \Carbon\Carbon::parse($track->outbound_time) 
                                    : now();
                                $duration = $inbound->diff($outbound);
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $track->permit->uniqueId ?? 'N/A' }}</td>
                                <td>{{ $inbound->toDayDateTimeString() }}</td>
                                <td>
                                    {{ $track->outbound_time 
                                        ? $outbound->toDayDateTimeString()
                                        : 'Still Parked' }}
                                </td>
                                <td>{{ $duration->format('%h hr %i min') }}</td>
                                <td>{{ $track->inboundAdmin->fName ?? 'N/A' }} {{ $track->inboundAdmin->lName ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
