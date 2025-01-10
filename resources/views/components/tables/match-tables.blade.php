<div class="card">
    <div class="card-body">
        <x-table :headers="['#','Team', 'Opposing Team', 'Score', 'Competition','Match Date','Venue', 'Match Type','Status', 'Action']" tableId="{{ $tableId }}"/>
    </div>
</div>

@push('addon-script')
    <script type="module">
        import { processWithConfirmation } from "{{ Vite::asset('resources/js/ajax-processing-data.js') }}";
        $(document).ready(function (){
            const datatable = $('#{{ $tableId }}').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{{ $route }}',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    { data: 'homeTeam', name: 'homeTeam' },
                    { data: 'awayTeam', name: 'awayTeam' },
                    { data: 'score', name: 'score' },
                    { data: 'competition', name: 'competition'},
                    { data: 'date', name: 'date' },
                    { data: 'place', name: 'place'},
                    { data: 'matchType', name: 'matchType'},
                    { data: 'status', name: 'status' },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [[5, 'asc']]
            });

            @if(isAllAdmin())
                processWithConfirmation(
                    '.delete',
                    "{{ route('match-schedules.destroy', ['schedule' => ':id']) }}",
                    "{{ route('match-schedules.index') }}",
                    'DELETE',
                    "Are you sure to delete this match?",
                    "Something went wrong when deleting this match!",
                    "{{ csrf_token() }}"
                 );
            @endif
        });
    </script>
@endpush
