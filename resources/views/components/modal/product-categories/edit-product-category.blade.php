<x-modal.form id="editProductCategoryModal" formId="formEditProductCategoryModal" title="Edit product category" :editForm="true" size="">
    <x-forms.basic-input type="hidden" name="productCategoryId" :modal="true"/>
    <x-forms.basic-input type="text" name="categoryName" label="product category Name" placeholder="Input product category name ..." :modal="true"/>
    <x-forms.textarea name="description" label="description" placeholder="Input product description ..." :modal="true" :required="false"/>
</x-modal.form>
@push('addon-script')
    <script>
        $(document).ready(function (){
            const formId = '#formEditProductCategoryModal';
            const modalId = '#editProductCategoryModal';
            const button = '.editProductCategory'

            $('body').on('click', button, function (e) {
                e.preventDefault();
                const id = $(this).attr('id');
                $.ajax({
                    url: "{{ route('product-categories.edit', ':id') }}".replace(':id', id),
                    type: 'GET',
                    success: function (res) {
                        $(modalId).modal('show')
                        clearModalFormValidation(formId)

                        $(formId+' .modal-title').text('Edit product category : '+res.data.productName);
                        $(formId+' #productCategoryId').val(res.data.id);
                        $.each(res.data, function (key, value) {
                            $(formId+' #'+key).val(value);
                        })
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
            processModalForm(formId, "{{ route('product-categories.update', ':id') }}", '#productCategoryId', modalId);
        });
    </script>
@endpush
