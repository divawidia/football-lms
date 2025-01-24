<div class="card">
    <div class="card-body">
        <x-table :headers="['#','Name', 'performance review', 'date created', 'last updated', 'Action']" tableId="{{ $tableId }}"/>
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
                    url: '{{ route('match-schedules.player-performance-review', ['schedule' => $match->hash]) }}',
                    @if($teamId)
                    data: {
                        teamId: {{ $teamId }},
                    }
                    @endif
                },
                pageLength: 5,
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    { data: 'name', name: 'name', width: '50%' },
                    { data: 'performance_review', name: 'performance_review', width: '50%' },
                    { data: 'performance_review_created', name: 'performance_review_created' },
                    { data: 'performance_review_last_updated', name: 'performance_review_last_updated'},
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
