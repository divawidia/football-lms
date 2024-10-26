<div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="parentsTable">
                <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Relation</th>
                    @if(isAllAdmin())
                        <th>Action</th>
                    @endif
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
        $(document).ready(function () {
            $('#parentsTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! route('player-parents.index', $player) !!}',
                },
                columns: [
                    {data: 'firstName', name: 'firstName'},
                    {data: 'lastName', name: 'lastName'},
                    {data: 'email', name: 'email'},
                    {data: 'phoneNumber', name: 'phoneNumber'},
                    {data: 'relations', name: 'relations'},
                        @if(isAllAdmin())
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                    @endif
                ]
            });
        });
    </script>
@endpush
