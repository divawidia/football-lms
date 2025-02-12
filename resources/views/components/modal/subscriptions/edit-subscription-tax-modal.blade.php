<x-modal.form id="editTaxModal" formId="formEditTaxModal" title="Edit Tax" :editForm="true" size="">
    <x-forms.basic-input type="hidden" name="subscriptionId"/>
    <x-forms.select name="taxId" label="Include Tax" :select2="true" :modal="true">
        <option disabled>Select tax</option>
        @foreach($taxes as $tax)
            <option value="{{ $tax->id }}">{{ $tax->taxName }} ~ {{ $tax->percentage }}%</option>
        @endforeach
        <option value="{{ null }}">Without tax included</option>
    </x-forms.select>
</x-modal.form>

@push('addon-script')
    <script>
        $(document).ready(function (){
            const body = $('body');
            const formId = '#formEditTaxModal';
            const modalId = '#editTaxModal';
            const button = '.edit-tax'

            body.on('click', button, function(e) {
                e.preventDefault();
                const id = $(this).attr('id');
                clearModalFormValidation(formId)
                $.ajax({
                    url: "{{ route('subscriptions.edit', ':id') }}".replace(':id', id),
                    type: 'get',
                    success: function(res) {
                        $(modalId).modal('show');
                        $(formId+' .modal-title').text('Edit '+res.data.user.firstName+' '+res.data.user.lastName+' subscription of '+res.data.product.productName+"'s tax");
                        $(formId+' #subscriptionId').val(res.data.id);
                        $.each(res.data, function (key, value) {
                            $(formId+' #'+key).val(value);
                        });
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

            processModalForm(formId, "{{ route('subscriptions.update-tax', ':id') }}", formId+" #subscriptionId", modalId);
        });
    </script>
@endpush
