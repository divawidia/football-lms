<div class="modal fade" id="editNoteModal" tabindex="-1" aria-labelledby="editNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
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
                        <label class="form-label" for="note">Note</label>
                        <small class="text-danger">*</small>
                        <textarea class="form-control" id="note" name="note" rows="10" required></textarea>
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
                                modalId="#editNoteModal"/>

@push('addon-script')
    <script>
        $(document).ready(function (){
            const formId = '#formUpdateNoteModal'
            $('.edit-note').on('click', function(e) {
                e.preventDefault();
                const id = $(this).attr('id');

                $.ajax({
                    url: "{{ $routeEdit }}".replace(':id', id),
                    type: 'get',
                    success: function(res) {
                        $('#editNoteModal').modal('show');
                        $(formId+' #note').text(res.data.note);
                        $(formId+' #noteId').val(res.data.id);
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
        });
    </script>
@endpush
