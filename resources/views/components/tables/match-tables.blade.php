<div class="card">
    <div class="card-body">
        <x-table :headers="['#','Team', 'Score', 'Opposing Team', 'Competition','Match Date','Venue', 'Match Type','Status', 'Action']" tableId="{{ $tableId }}"/>
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
                    url: '{{ $route }}',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    { data: 'homeTeam', name: 'homeTeam' },
                    { data: 'score', name: 'score' },
                    { data: 'awayTeam', name: 'awayTeam' },
                    { data: 'competition', name: 'competition'},
                    { data: 'date', name: 'date' },
                    { data: 'place', name: 'place'},
                    { data: 'matchType', name: 'matchType'},
                    { data: 'status', name: 'status' },
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
            });

            @if(isAllAdmin())
                processWithConfirmation(
                    '.delete',
                    "{{ route('match-schedules.destroy', ['match' => ':id']) }}",
                    "{{ route('match-schedules.index') }}",
                    'DELETE',
                    "Are you sure to delete this match?",
                    "Something went wrong when deleting this match!",
                    "{{ csrf_token() }}"
                 );
            processWithConfirmation(
                '.cancelBtn',
                "{{ route('match-schedules.cancel', ['match' =>':id']) }}",
                "{{ route('match-histories.index') }}",
                'PATCH',
                "Are you sure to cancel this match?",
                "Something went wrong when cancelling this match!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.scheduled-btn',
                "{{ route('match-schedules.scheduled', ['match' =>':id']) }}",
                "{{ route('match-histories.index') }}",
                'PATCH',
                "Are you sure to set this match to scheduled?",
                "Something went wrong when set this match to scheduled!",
                "{{ csrf_token() }}"
            );
            @endif
        });
    </script>
@endpush
