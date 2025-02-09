<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover w-100" id="{{ $tableId }}">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Event</th>
                    <th>performance review</th>
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
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    { data: 'event', name: 'event'},
                    { data: 'performance_review', name: 'performance_review', width: '50%' },
                    { data: 'performance_review_created', name: 'performance_review_created' },
                    { data: 'performance_review_last_updated', name: 'performance_review_last_updated'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '5%'
                    },
                ],
            });
        });
    </script>
@endpush
