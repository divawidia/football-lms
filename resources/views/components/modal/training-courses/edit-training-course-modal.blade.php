<x-modal.form id="editTrainingVideoModal" formId="formEditTrainingVideoModal" title="Edit Training Course" :editForm="true" size="">
    <x-forms.basic-input type="hidden" name="trainingId"/>
    <x-forms.basic-input type="text" name="trainingTitle" label="Training Title" placeholder="Input training's title ..." :modal="true"/>
    <x-forms.image-input-with-preview name="previewPhoto" label="Training Preview Image" :required="false" :modal="true"/>
    <x-forms.select name="level" label="Training course difficulty level" :modal="true">
        <option disabled>Select training course's difficulty</option>
        @foreach(['Beginner', 'Intermediate', 'Expert'] AS $level)
            <option value="{{ $level }}">{{ $level }}</option>
        @endforeach
    </x-forms.select>
    <x-forms.textarea name="description" label="Description"  placeholder="Input training course description ..."  :required="false" :modal="true"/>
</x-modal.form>

@push('addon-script')
    <script>
        $(document).ready(function (){
            const modalId = '#editTrainingVideoModal'
            const formId = '#formEditTrainingVideoModal'
            const buttonId = '#editTrainingVideo'

            showModal(buttonId, modalId, formId, function (e) {
                $.ajax({
                    url: "{{ $routeEdit }}",
                    type: 'GET',
                    success: function (res) {
                        $(modalId+' .modal-title').text('Edit Training Course: ' + res.data.trainingTitle);
                        $(formId+' #trainingId').val(res.data.id);
                        $(formId+' #trainingTitle').val(res.data.trainingTitle);
                        $(formId+' #level').val(res.data.level);
                        $(formId+' #description').text(res.data.description);
                        $(formId+' #preview').attr('src', "{{ Storage::url('') }}" + res.data.previewPhoto).addClass('d-block');
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when deleting data!",
                            text: errorThrown,
                        });
                    }
                });
            })
            processModalForm(formId, "{{ $routeUpdate }}", null, modalId);
        });
    </script>
@endpush
