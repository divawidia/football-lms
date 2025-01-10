<x-modal.form id="createNoteModal" formId="formCreateNoteModal" title="Create Session Note">
    <x-forms.basic-input type="hidden" name="teamId" :modal="true"/>

    <x-forms.textarea name="note" label="Session Note" placeholder="Input the session note ..." :modal="true"/>
</x-modal.form>

@push('addon-script')
    <script type="module">
        import { processModalForm } from "{{ Vite::asset('resources/js/ajax-processing-data.js') }}";
        import { clearModalFormValidation } from "{{ Vite::asset('resources/js/modal.js') }}";

        $(document).ready(function (){
            const formId = '#formCreateNoteModal';
            const modalId = '#createNoteModal';

            $('.add-new-note-btn').on('click', function(e) {
                e.preventDefault();
                const team = $(this).attr('id')

                $(modalId).modal('show');
                clearModalFormValidation(formId)
                $(formId+' #teamId').val(team)
            });

            processModalForm(
                formId,
                "{{ route('match-schedules.create-note', $schedule->hash) }}",
                "",
                modalId
            );
        });
    </script>
@endpush
