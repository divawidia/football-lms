<x-modal.form id="addTrainingVideoModal" formId="formAddTrainingVideoModal" title="Create new training Course" :editForm="false" size="">
    <x-forms.basic-input type="text" name="trainingTitle" label="Training Title" placeholder="Input training's title ..." :modal="true"/>
    <x-forms.image-input-with-preview name="previewPhoto" label="Training Preview Image" :required="false" :modal="true"/>
    <x-forms.select name="level" label="Training course difficulty level" :modal="true">
        <option disabled selected>Select training course's difficulty</option>
        @foreach(['Beginner', 'Intermediate', 'Expert'] AS $level)
            <option value="{{ $level }}">{{ $level }}</option>
        @endforeach
    </x-forms.select>
    <x-forms.textarea name="description" label="Description" placeholder="Input training course description ..." :required="false" :modal="true"/>
</x-modal.form>

@push('addon-script')
    <script>
        $(document).ready(function (){
            const modalId = '#addTrainingVideoModal'
            const formId = '#formAddTrainingVideoModal'
            const buttonId = '#addTrainingVideo'

            showModal(buttonId, modalId, formId)
            processModalForm(formId, "{{ route('training-videos.store') }}", null, modalId);
        });
    </script>
@endpush
