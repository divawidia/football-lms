<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="{{ $tableId }}">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Training/Practice</th>
                    <th>Team</th>
                    <th>training date</th>
                    <th>Location</th>
                    <th>Training Status</th>
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
        $(document).ready(function (){
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
                // order: [[2, 'desc']],
            });
        });
    </script>
@endpush
