<x-modal.form id="editNoteModal" formId="formUpdateNoteModal" title="Edit Session Note" :editForm="true">

    <x-forms.basic-input type="hidden" name="noteId" :modal="true"/>

    <x-forms.textarea name="note" label="Session Note" placeholder="Input the session note ..." :modal="true"/>

</x-modal.form>

@push('addon-script')
    <script type="module">
        import { processModalForm } from "{{ Vite::asset('resources/js/ajax-processing-data.js') }}";
        import { clearModalFormValidation } from "{{ Vite::asset('resources/js/modal.js') }}";

        $(document).ready(function (){
            const formId = '#formUpdateNoteModal'
            const modalId = '#editNoteModal';

            $('.edit-note').on('click', function(e) {
                e.preventDefault();
                const id = $(this).attr('id');

                $.ajax({
                    url: "{{ route('match-schedules.edit-note', ['schedule' => $eventSchedule->hash, 'note' => ':id']) }}".replace(':id', id),
                    type: 'get',
                    success: function(res) {
                        $(modalId).modal('show');
                        clearModalFormValidation(formId)

                        $(formId+' #note').text(res.data.note);
                        $(formId+' #noteId').val(id);
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

            processModalForm(
                formId,
                "{{ route('match-schedules.update-note', ['schedule' => $eventSchedule->hash, 'note' => ':id']) }}",
                "#noteId",
                modalId
            );
        });
    </script>
@endpush
