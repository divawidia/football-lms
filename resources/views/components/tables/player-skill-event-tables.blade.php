<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="{{ $tableId }}">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>skill stats status</th>
                    <th>skill stats created</th>
                    <th>skill stats last updated</th>
                    <th>performance review</th>
                    <th>performance review created</th>
                    <th>performance review last updated</th>
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
            const datatable = $('#{{ $tableId }}').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{{ $route }}',
                },
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'stats_status', name: 'stats_status' },
                    { data: 'stats_created', name: 'stats_created' },
                    { data: 'stats_updated', name: 'stats_updated' },
                    { data: 'performance_review', name: 'performance_review', width: '50%' },
                    { data: 'performance_review_created', name: 'performance_review_created' },
                    { data: 'performance_review_last_updated', name: 'performance_review_last_updated'},
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
