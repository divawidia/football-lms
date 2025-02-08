<x-modal.form id="addLessonModal" formId="formAddLessonModal" title="Add lesson to Training Course" :editForm="false" size="">
    <x-forms.basic-input type="text" name="lessonTitle" label="lesson Title" placeholder="Input lesson's title ..." :modal="true"/>
    <x-forms.textarea name="description" label="Description" placeholder="Input lesson's description ..." :required="false" :modal="true"/>
    <x-forms.basic-input type="text" name="lessonVideoURL" label="Lesson Video URL" placeholder="Input youtube video url (only from youtube!) ..." :modal="true"/>
    <div id="preview-container">
        <div id="create-player"></div>
    </div>
    <input type="hidden" id="totalDuration" name="totalDuration" required class="totalDuration">
    <x-forms.basic-input type="hidden" name="videoId"/>
</x-modal.form>

@push('addon-script')
    <script>
        $(document).ready(function (){
            const modalId = '#addLessonModal'
            const formId = '#formAddLessonModal'

            showModal('#addLesson', modalId, formId)
            processModalForm(formId, "{{ $routeStore }}", null, modalId);
        });
    </script>
@endpush
