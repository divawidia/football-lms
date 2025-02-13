<x-modal.form id="addTaxModal" formId="formAddTaxModal" title="Create new tax" :editForm="false" size="">
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
            const formId = '#formAddTaxModal';
            const modalId = '#addTaxModal';
            showModal('.addTax', modalId, formId)
            processModalForm(formId, "{{ route('taxes.store') }}", null, modalId);
        });
    </script>
@endpush
