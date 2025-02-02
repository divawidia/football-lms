<div class="card">
    <div class="card-body">
        <x-table
            :headers="['#','Training/Practice', 'Team', 'Training Date', 'Location', 'Status', 'Action']"
            :tableId="$tableId"
        />
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
                    { data: 'topic', name: 'topic' },
                    { data: 'team', name: 'team' },
                    { data: 'date', name: 'date' },
                    { data: 'location', name: 'location'},
                    { data: 'status', name: 'status' },
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [[2, 'asc']]
            });

            processWithConfirmation(
                '.delete',
                "{{ route('training-schedules.destroy', ['schedule' => ':id']) }}",
                "{{ route('training-schedules.index') }}",
                'DELETE',
                "Are you sure to delete this training session?",
                "Something went wrong when deleting this training session!",
                "{{ csrf_token() }}"
            );
        });
    </script>
@endpush
