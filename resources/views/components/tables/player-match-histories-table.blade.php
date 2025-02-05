<div class="card">
    <div class="card-body">
        <x-table :headers="['#', 'Team', 'Opposing Team', 'Match Date', 'Venue', 'Competition', 'Match Type', 'Match Status', 'Attendance Status', 'Attendance Note', 'Last Updated Attendance', 'Action']" tableId="matchHistoryTable"/>
    </div>
</div>

@push('addon-script')
    <script>
        $(document).ready(function() {
            $('#matchHistoryTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: "{{ route('attendance-report.player-match-index', $player->hash) }}",
                },
                pageLength: 5,
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    { data: 'team', name: 'team' },
                    { data: 'opponentTeam', name: 'opponentTeam' },
                    { data: 'date', name: 'date' },
                    { data: 'place', name: 'place'},
                    { data: 'competition', name: 'competition'},
                    { data: 'matchType', name: 'matchType'},
                    { data: 'status', name: 'status' },
                    { data: 'attendanceStatus', name: 'attendanceStatus' },
                    { data: 'note', name: 'note' },
                    { data: 'last_updated', name: 'last_updated' },
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [[3, 'desc']],
            });
        });
    </script>
@endpush
