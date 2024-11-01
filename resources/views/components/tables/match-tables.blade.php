<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="{{ $tableId }}">
                <thead>
                <tr>
                    <th>Team</th>
                    <th>Opponent</th>
                    <th>Match Date</th>
                    <th>Location</th>
                    <th>Competition</th>
                    <th>Match Type</th>
                    <th>Status</th>
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
                    { data: 'team', name: 'team' },
                    { data: 'opponentTeam', name: 'opponentTeam' },
                    { data: 'date', name: 'date' },
                    { data: 'place', name: 'place'},
                    { data: 'competition', name: 'competition'},
                    { data: 'matchType', name: 'matchType'},
                    { data: 'status', name: 'status' },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ],
                order: [[2, 'asc']]
            });

            @if(isAllAdmin())
            $('body').on('click', '.delete', function() {
                let id = $(this).attr('id');

                Swal.fire({
                    title: "Are you sure to delete this match?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            @if(isAllAdmin())
                            url: "{{ route('match-schedules.destroy', ['schedule' => ':id']) }}".replace(':id', id),
                            @endif
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Match successfully deleted!",
                                });
                                datatable.ajax.reload();
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
