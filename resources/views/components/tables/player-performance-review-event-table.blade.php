<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover w-100" id="{{ $tableId }}">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
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
                    @if($teamId)
                    data: {
                        teamId: {{ $teamId }},
                    }
                    @endif
                },
                pageLength: 5,
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    { data: 'name', name: 'name', width: '50%' },
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
