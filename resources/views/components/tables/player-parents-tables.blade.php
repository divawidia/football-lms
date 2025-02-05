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

@push('addon-script')
    <script>
        $(document).ready(function () {
            $('#parentsTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! route('player-managements.player-parents.index', $player) !!}',
                },
                columns: [
                    {data: 'firstName', name: 'firstName'},
                    {data: 'lastName', name: 'lastName'},
                    {data: 'email', name: 'email'},
                    {data: 'phoneNumber', name: 'phoneNumber'},
                    {data: 'relations', name: 'relations'},
                    @if(isAllAdmin()) {data: 'action', name: 'action', orderable: false, searchable: false}, @endif
                ]
            });

            @if(isAllAdmin())
                processWithConfirmation(
                    ".delete-parent",
                    "{{ route('player-managements.player-parents.destroy', ['player' => $player->hash, 'parent' => ':id']) }}",
                    "{{ route('player-managements.show', $player->hash) }}",
                    "DELETE",
                    "Are you sure to delete player {{ getUserFullName($player->user) }}'s parent/guardian?",
                    "Something went wrong when removing player {{ getUserFullName($player->user) }}'s parent/guardian!",
                    "{{ csrf_token() }}"
                );
            @endif
        });
    </script>
@endpush
