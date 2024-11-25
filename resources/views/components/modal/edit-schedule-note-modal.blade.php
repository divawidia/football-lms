<div class="modal fade" id="editNoteModal" tabindex="-1" aria-labelledby="editNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="post" id="formUpdateNoteModal">
                @method('PUT')
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="coachName">Update note for {{ $eventName }} Session</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="noteId">
                    <div class="form-group">
                        <label class="form-label" for="edit_note">Note</label>
                        <small class="text-danger">*</small>
                        <textarea class="form-control" id="edit_note" name="note" placeholder="Input note here ..." required></textarea>
                        <span class="invalid-feedback note_error" role="alert">
                                <strong></strong>
                            </span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<x-modal-form-update-processing formId="#formUpdateNoteModal"
                                updateDataId="#noteId"
                                :routeUpdate="$routeUpdate"
                                modalId="#editNoteModal"
                                :routeAfterProcess="$routeAfterProcess"/>

@push('addon-script')
    <script>
        $(document).ready(function (){
            $('.edit-note').on('click', function(e) {
                e.preventDefault();
                const id = $(this).attr('id');

                $.ajax({
                    {{--url: "{{ route('training-schedules.edit-note', ['schedule' => $data['dataSchedule']->id, 'note' => ":id"]) }}".replace(':id', id),--}}
                    url: "{{ $routeEdit }}".replace(':id', id),
                    type: 'get',
                    success: function(res) {
                        $('#editNoteModal').modal('show');

                        const heading = document.getElementById('edit_note');
                        heading.textContent = res.data.note;
                        $('#noteId').val(res.data.id);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when deleting data!",
                            text: errorThrown,
                        });
                    }
                });
            });

            {{--// update schedule note data--}}
            {{--$('#formUpdateNoteModal').on('submit', function(e) {--}}
            {{--    e.preventDefault();--}}
            {{--    const id = $('#noteId').val();--}}
            {{--    $.ajax({--}}
            {{--        --}}{{--url: "{{ route('training-schedules.update-note', ['schedule' => $data['dataSchedule']->id, 'note' => ":id"]) }}".replace(':id', id),--}}
            {{--        url: "{{ $routeUpdate }}".replace(':id', id),--}}
            {{--        type: $(this).attr('method'),--}}
            {{--        data: new FormData(this),--}}
            {{--        contentType: false,--}}
            {{--        processData: false,--}}
            {{--        success: function(res) {--}}
            {{--            $('#editNoteModal').modal('hide');--}}
            {{--            Swal.fire({--}}
            {{--                title: 'Training session note successfully updated!',--}}
            {{--                icon: 'success',--}}
            {{--                showCancelButton: false,--}}
            {{--                allowOutsideClick: false,--}}
            {{--                confirmButtonColor: "#1ac2a1",--}}
            {{--                confirmButtonText:--}}
            {{--                    'Ok!'--}}
            {{--            }).then((result) => {--}}
            {{--                if (result.isConfirmed) {--}}
            {{--                    location.reload();--}}
            {{--                }--}}
            {{--            });--}}
            {{--        },--}}
            {{--        error: function(xhr) {--}}
            {{--            const response = JSON.parse(xhr.responseText);--}}
            {{--            console.log(response);--}}
            {{--            $.each(response.errors, function(key, val) {--}}
            {{--                $('span.' + key + '_error').text(val[0]);--}}
            {{--                $("#edit_" + key).addClass('is-invalid');--}}
            {{--            });--}}
            {{--        }--}}
            {{--    });--}}
            {{--});--}}
        });
    </script>
@endpush
