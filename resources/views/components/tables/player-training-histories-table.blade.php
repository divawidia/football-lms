<div class="card">
    <div class="card-body">
        <x-table :headers="['#', 'Training/Practice', 'Team', 'Training Date', 'Location', 'Training Status', 'Attendance Status', 'Attendance Note', 'Last Updated Attendance', 'Action']" tableId="trainingHistoryTable"/>
    </div>
</div>

@push('addon-script')
    <script>
        $(document).ready(function (){
            $('#trainingHistoryTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{{ route('attendance-report.player-training-index', $player->hash) }}',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    { data: 'topic', name: 'topic' },
                    { data: 'team', name: 'team' },
                    { data: 'date', name: 'date' },
                    { data: 'location', name: 'location'},
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
