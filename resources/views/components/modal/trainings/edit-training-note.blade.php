<x-modal.form id="editNoteModal" formId="formUpdateNoteModal" title="Edit Training Session Note" :editForm="true" size="">

    <x-forms.basic-input type="hidden" name="noteId" :modal="true"/>

    <x-forms.textarea name="note" label="Training Session Note" placeholder="Input the Training session note ..." :modal="true"/>

</x-modal.form>

@push('addon-script')
    <script>
        $(document).ready(function (){
            const formId = '#formUpdateNoteModal'
            const modalId = '#editNoteModal';

            $('.edit-note').on('click', function(e) {
                e.preventDefault();
                const id = $(this).attr('id');

                $.ajax({
                    url: "{{ route('training-schedules.edit-note', ['training' => $training->hash, 'note' => ':id']) }}".replace(':id', id),
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
                "{{ route('training-schedules.update-note', ['training' => $training->hash, 'note' => ':id']) }}",
                "#noteId",
                modalId
            );
        });
    </script>
@endpush
