<x-modal.form id="addProductModal" formId="formAddProductModal" title="Create new product" :editForm="false" size="">
    <x-forms.basic-input type="text" name="productName" label="product Name" placeholder="Input product name ..." :modal="true"/>
    <x-forms.input-with-prepend-append name="price" label="price" placeholder="Input product price ..." text="Rp." :append="false"/>
    <div class="form-group">
        <div class="d-flex flex-row align-items-center mb-2">
            <label class="form-label" for="categoryId">Product Category</label>
            <small class="text-danger">*</small>
            <x-buttons.basic-button icon="add" text="Add New category" additionalClass="addProductCategory" color="primary" size="sm" iconColor="" margin="ml-auto"/>
        </div>
        @if(count($categories) == 0)
            <x-warning-alert text="Currently you haven't created any product categories, please create your product categories"/>
        @else
            <select class="form-control form-select" id="categoryId" name="categoryId" required data-toggle="select">
                <option disabled selected>Select product's category</option>
                @foreach($categories AS $category)
                    <option value="{{ $category->id }}">{{ $category->categoryName }}</option>
                @endforeach
            </select>
            <span class="invalid-feedback categoryId_error" role="alert"><strong></strong></span>
        @endif
    </div>
    <x-forms.textarea name="description" label="description" placeholder="Input product description ..." :modal="true" :required="false"/>
    <x-forms.select name="priceOption" label="Payment Type">
        <option disabled selected>Select product's payment type</option>
        @foreach(['subscription', 'one time payment'] AS $payment)
            <option value="{{ $payment }}">{{ $payment }}</option>
        @endforeach
    </x-forms.select>
    <div class="subscriptionCycleForm">
        <x-forms.select name="subscriptionCycle" label="Subscription Cycle">
            <option disabled value="(NULL)" selected>Select product's subscription cycle</option>
            @foreach(['monthly', 'quarterly', 'semianually', 'anually'] AS $cycle)
                <option value="{{ $cycle }}">{{ $cycle }}</option>
            @endforeach
        </x-forms.select>
    </div>
</x-modal.form>

@push('addon-script')
    <script>
        $(document).ready(function (){
            const formId = '#formAddProductModal';
            const modalId = '#addProductModal';
            $(formId+' .subscriptionCycleForm').hide();
            showModal('.addProducts', modalId, formId)
            processModalForm(formId, "{{ route('products.store') }}", null, modalId);
        });
    </script>
@endpush
