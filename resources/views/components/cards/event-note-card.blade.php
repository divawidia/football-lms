<div class="card">
    <div class="card-header d-flex align-items-center">
        <div class="flex">
            <h4 class="card-title">{{ date('D, M d Y h:i A', strtotime($note->created_at)) }}</h4>
            <div class="card-subtitle text-50">Last updated at {{ date('D, M d Y h:i A', strtotime($note->updated_at)) }}</div>
        </div>
        @if(isAllAdmin() || isCoach())
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="material-icons">more_vert</span>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item edit-note" id="{{ $note->id }}" href="">
                        <span class="material-icons">edit</span>Edit Note
                    </a>
                    <button type="button" class="dropdown-item delete-note" id="{{ $note->id }}">
                        <span class="material-icons">delete</span>Delete Note
                    </button>
                </div>
            </div>
        @endif
    </div>
    <div class="card-body">
        @php
            echo $note->note
        @endphp
    </div>
</div>

@push('addon-script')
    <script>
        $(document).ready(function () {
            const body = $('body');

            // delete schedule note alert
            body.on('click', '.delete-note', function () {
                let id = $(this).attr('id');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ $deleteRoute }}".replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function () {
                                Swal.fire({
                                    title: 'Training note successfully deleted!',
                                    icon: 'success',
                                    showCancelButton: false,
                                    allowOutsideClick: false,
                                    confirmButtonColor: "#1ac2a1",
                                    confirmButtonText:
                                        'Ok!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                });
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Something went wrong when deleting data!",
                                    text: errorThrown,
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
