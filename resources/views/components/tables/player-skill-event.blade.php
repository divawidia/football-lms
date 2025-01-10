<div class="card">
    <div class="card-body">
        <x-table :headers="['Name', 'skill stats status', 'date created', 'last updated', 'Action']" tableId="{{ $tableId }}"/>
    </div>
</div>

@push('addon-script')
    <script>
        $(document).ready(function (){
            $('#{{ $tableId }}').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{{ route('match-schedules.player-skills', ['schedule' => $eventSchedule->hash]) }}',
                    @if($teamId)
                        data: {
                            teamId: {{ $teamId }},
                        }
                    @endif

                },
                pageLength: 5,
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'stats_status', name: 'stats_status' },
                    { data: 'stats_created', name: 'stats_created' },
                    { data: 'stats_updated', name: 'stats_updated' },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
            });
        });
    </script>
@endpush
