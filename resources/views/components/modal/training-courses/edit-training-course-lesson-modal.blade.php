<x-modal.form id="editLessonModal" formId="formEditLessonModal" title="Edit Lesson" :editForm="true" size="">
    <x-forms.basic-input type="hidden" name="lessonId"/>
    <x-forms.basic-input type="text" name="lessonTitle" label="Lesson Title" placeholder="Input lesson's title ..." :modal="true"/>
    <x-forms.textarea name="description" label="Description" placeholder="Input lesson's description ..."  :required="false" :modal="true"/>
    <x-forms.basic-input type="text" name="lessonVideoURL" label="Lesson Video URL" placeholder="Input youtube video url (only from youtube!) ..." :modal="true"/>
    <div id="preview-container" data-value="2131">
        <div id="edit-player"></div>
    </div>
    <input type="hidden" id="totalDuration" name="totalDuration" required class="totalDuration">
    <x-forms.basic-input type="hidden" name="videoId"/>
</x-modal.form>

@push('addon-script')
    <script>
        $(document).ready(function (){
            const body = $('body');
            const modalId = '#editLessonModal'
            const formId = '#formEditLessonModal'
            const button = '.editLesson'

            body.on('click', button, function (e) {
                e.preventDefault();
                const id = $(this).attr('id');
                $.ajax({
                    url: "{{ route('training-videos.lessons-edit', ['trainingVideo'=>$trainingVideo->hash, 'lesson' => ':id']) }}".replace(':id', id),
                    type: 'GET',
                    success: function (res) {
                        $(modalId).modal('show');
                        $(formId+' .modal-title').text('Edit Lesson : ' + res.data.lessonTitle);
                        $(formId+' #lessonId').val(res.data.id);
                        $(formId+' #lessonTitle').val(res.data.lessonTitle);
                        $(formId+' #description').text(res.data.description);
                        $(formId+' #lessonVideoURL').val(res.data.lessonVideoURL);
                        $(formId+' #totalDuration').val(res.data.totalDuration);
                        $(formId+' #videoId').val(res.data.videoId);
                        $(formId+' #edit-player').remove();
                        $(formId+' #preview-container').html('<div id="edit-player"></div>')
                        onYouTubeIframeAPIReadyForAdmin(res.data.videoId, 'edit-player');
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

            processModalForm(formId, '{{ route('training-videos.lessons-update', ['trainingVideo'=>$trainingVideo->hash, 'lesson' => ':id']) }}', '#lessonId', modalId);
        });
    </script>
@endpush
