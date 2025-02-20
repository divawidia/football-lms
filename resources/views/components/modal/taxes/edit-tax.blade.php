<x-modal.form id="editTaxModal" formId="formEditTaxModal" title="Edit tax" :editForm="true" size="">
    <x-forms.basic-input type="hidden" name="taxId" :modal="true"/>
    <div class="row">
        <div class="col-6">
            <x-forms.basic-input type="text" name="taxName" label="tax Name" placeholder="Input tax name ..." :modal="true"/>
        </div>
        <div class="col-6">
            <x-forms.input-with-prepend-append name="percentage" label="tax percentage" placeholder="Input tax percentage ..." text="%"/>
        </div>
    </div>
    <x-forms.textarea name="description" label="description" placeholder="Input tax description ..." :modal="true" :required="false"/>
</x-modal.form>

@push('addon-script')
    <script>
        $(document).ready(function (){
            const formId = '#formEditTaxModal';
            const modalId = '#editTaxModal';
            const button = '.editTax'

            $('body').on('click', button, function (e) {
                e.preventDefault();
                const id = $(this).attr('id');
                $.ajax({
                    url: "{{ route('taxes.edit', ':id') }}".replace(':id', id),
                    type: 'GET',
                    success: function (res) {
                        $(modalId).modal('show')
                        clearModalFormValidation(formId)

                        $(formId+' .modal-title').text('Edit tax : '+res.data.taxName);
                        $(formId+' #taxId').val(res.data.id);
                        $.each(res.data, function (key, value) {
                            $(formId+' #'+key).val(value);
                        })
                        subscriptionCycleDisplay(formId);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when updating data!",
                            text: errorThrown,
                        });
                    }
                })
            })
            processModalForm(formId, "{{ route('taxes.update', ':id') }}", '#taxId', modalId);
        });
    </script>
@endpush
