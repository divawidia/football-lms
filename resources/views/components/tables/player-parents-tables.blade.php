<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover w-100" id="parentsTable">
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
@if(isAllAdmin())
<x-process-data-confirmation btnClass=".delete-parent"
                             :processRoute="route('player-parents.destroy', ['player' => $player->id, 'parent' => ':id'])"
                             :routeAfterProcess="route('player-managements.show', $player->id)"
                             method="DELETE"
                             confirmationText="Are you sure to to delete player {{ getUserFullName($player->user) }}'s parent/guardian?"
                             errorText="Something went wrong when removing player {{ getUserFullName($player->user) }}'s parent/guardian!"/>
@endif
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
