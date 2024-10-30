<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="{{ $tableId }}">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>skill stats status</th>
                    <th>date created</th>
                    <th>last updated</th>
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
                    url: '{{ $route }}',
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
                        searchable: false,
                        width: '15%'
                    },
                ],
            });
        });
    </script>
@endpush
