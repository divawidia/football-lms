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
            const parentsTable = $('#parentsTable').DataTable({
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

            @if(isAllAdmin())
            $('body').on('click', '.delete-parent', function () {
                const idParent = $(this).attr('id');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert after delete this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('player-parents.destroy', ['player' => $player, 'parent' => ':idParent']) }}".replace(':idParent', idParent),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function () {
                                Swal.fire({
                                    icon: "success",
                                    title: "Player's parent/guardian successfully deleted!",
                                });
                                parentsTable.ajax.reload();
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Something went wrong when deleting data!",
                                    text: errorThrown
                                });
                            }
                        });
                    }
                });
            });
            @endif

        });
    </script>
@endpush
