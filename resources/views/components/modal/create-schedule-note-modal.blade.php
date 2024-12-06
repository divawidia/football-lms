<div class="modal fade" id="createNoteModal" tabindex="-1" aria-labelledby="createNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ $routeCreate }}" method="post" id="formCreateNoteModal">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="coachName">Create note for {{ $eventName }} Session</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="teamId" name="teamId">
                    <div class="form-group">
                        <label class="form-label" for="note">Note</label>
                        <small class="text-danger">*</small>
                        <textarea class="form-control" id="note" name="note" placeholder="Input note here ..." rows="5"></textarea>
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

<x-modal-form-update-processing formId="#formCreateNoteModal"
                                updateDataId=""
                                :routeUpdate="$routeCreate"
                                modalId="#createNoteModal"/>

@push('addon-script')
    <script>
        $(document).ready(function (){
            $('.addNewNote').on('click', function(e) {
                e.preventDefault();
                const team = $(this).attr('data-team')
                $('#createNoteModal').modal('show');
                $('#formCreateNoteModal #teamId').val(team)
            });
        });
    </script>
@endpush
