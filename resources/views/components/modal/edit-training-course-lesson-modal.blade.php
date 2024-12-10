<!-- Modal add lesson -->
<div class="modal fade" id="editLessonModal" tabindex="-1" aria-labelledby="editLessonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" id="formEditLessonModal">
                @method('PUT')
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="lessonFormTitle"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="lessonId" name="lessonId">
                    <div class="form-group">
                        <label class="form-label" for="edit-lessonTitle">Lesson Title</label>
                        <small class="text-danger">*</small>
                        <input type="text"
                               id="edit-lessonTitle"
                               name="lessonTitle"
                               class="form-control"
                               placeholder="Input lesson's title ..."
                        >
                        <span class="invalid-feedback lessonTitle_error" role="alert">
                            <strong></strong>
                        </span>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit-description">Description</label>
                        <small class="text-sm">(Optional)</small>
                        <textarea class="form-control" id="edit-description" name="description"></textarea>
                        <span class="invalid-feedback description_error" role="alert">
                            <strong></strong>
                        </span>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit-lessonVideoURL">Lesson Video URL</label>
                        <small class="text-danger">*</small>
                        <input type="text"
                               id="edit-lessonVideoURL"
                               name="lessonVideoURL"
                               class="form-control"
                               placeholder="Input youtube video url (only from youtube!) ..."
                        >
                        <span class="invalid-feedback lessonVideoURL_error" role="alert">
                            <strong></strong>
                        </span>
                        <div id="preview-container" data-value="2131">
                            <div id="edit-player"></div>
                        </div>
                    </div>
                    <input type="hidden" class="totalDuration" name="totalDuration">
                    <span class="invalid-feedback totalDuration_error" role="alert">
                        <strong></strong>
                    </span>
                    <input type="hidden" id="videoId" name="videoId">
                    <span class="invalid-feedback videoId_error" role="alert">
                        <strong></strong>
                    </span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('addon-script')
    <script type="module">
        import { onYouTubeIframeAPIReady } from "{{ Vite::asset('resources/js/youtube.js') }}";
        import { processModalForm } from "{{ Vite::asset('resources/js/ajax-processing-data.js') }}";

        $(document).ready(function (){
            const body = $('body');
            const formId = $('#formEditLessonModal');

            // show edit form modal when edit lesson button clicked
            body.on('click', '.editLesson', function (e) {
                e.preventDefault();
                const id = $(this).attr('id');
                $.ajax({
                    url: "{{ route('training-videos.lessons-edit', ['trainingVideo'=>$trainingVideo->hash, 'lesson' => ':id']) }}".replace(':id', id),
                    type: 'GET',
                    success: function (res) {
                        $('#editLessonModal').modal('show');

                        $(formId+' #lessonFormTitle').text('Edit Lesson ' + res.data.lessonTitle);
                        $(formId+' #lessonId').val(res.data.id);
                        $(formId+' #edit-lessonTitle').val(res.data.lessonTitle);
                        $(formId+' #edit-description').text(res.data.description);
                        $(formId+' #edit-lessonVideoURL').val(res.data.lessonVideoURL);
                        $(formId+' .totalDuration').val(res.data.totalDuration);
                        $(formId+' #videoId').val(res.data.videoId);
                        $(formId+' #edit-player').remove();
                        $(formId+' #preview-container').html('<div id="edit-player"></div>')
                        onYouTubeIframeAPIReady(res.data.videoId, 'edit-player');
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when deleting data!",
                            text: errorThrown,
                        });
                    }
                });
            });

            processModalForm(
                formId,
                '{{ route('training-videos.lessons-update', ['trainingVideo'=>$trainingVideo->hash, 'lesson' => ':id']) }}',
                '#lessonId',
                '#editLessonModal'
            )
        });
    </script>
@endpush
