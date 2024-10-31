<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="{{ $tableId }}">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Team</th>
                    <th>Opponent</th>
                    <th>Match Date</th>
                    <th>Location</th>
                    <th>Competition</th>
                    <th>Match Type</th>
                    <th>Match Status</th>
                    <th>Attendance Status</th>
                    <th>Note</th>
                    <th>Last Updated Attendance</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('addon-script')
    <script>
        $(document).ready(function() {
            $('#{{ $tableId }}').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! $tableRoute !!}',
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
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ],
                // order: [[2, 'desc']],
            });
        });
    </script>
@endpush
