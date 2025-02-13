<x-modal.form id="addProductCategoryModal" formId="formAddProductCategoryModal" title="Create new product category" :editForm="false" size="">
    <x-forms.basic-input type="text" name="categoryName" label="product category Name" placeholder="Input product category name ..." :modal="true"/>
    <x-forms.textarea name="description" label="description" placeholder="Input product description ..." :modal="true" :required="false"/>
</x-modal.form>

@push('addon-script')
    <script>
        $(document).ready(function (){
            const formId = '#formAddProductCategoryModal';
            const modalId = '#addProductCategoryModal';
            $('.addProductCategory').on('click', function (e) {
                e.preventDefault();
                $('#addProductModal').modal('hide');
                $(modalId).modal('show');
                clearModalFormValidation(formId)
            });
            processModalForm(formId, "{{ route('product-categories.store') }}", null, modalId);
        });
    </script>
@endpush
