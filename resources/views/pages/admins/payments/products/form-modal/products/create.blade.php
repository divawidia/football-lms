<!-- Modal add lesson -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="formAddProductModal">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="training-title">Create new products</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="productName">Product Name</label>
                        <small class="text-danger">*</small>
                        <input type="text"
                               id="productName"
                               name="productName"
                               class="form-control"
                               placeholder="Input product's name ..."
                               required>
                        <span class="invalid-feedback productName" role="alert">
                            <strong></strong>
                        </span>
                    </div>
                    <div class="form-group">
                        <div class="d-flex flex-row align-items-center mb-2">
                            <label class="form-label" for="categoryId">Product Category</label>
                            <small class="text-danger">*</small>
                            <button type="button"  class="btn btn-primary btn-sm ml-auto addProductCategory">
                                <span class="material-icons mr-2">add</span>
                                Add new
                            </button>
                        </div>
                        @if(count($categories) == 0)
                            <div class="alert alert-light border-1 border-left-4 border-left-accent"
                                 role="alert">
                                <div class="d-flex flex-wrap align-items-center">
                                    <i class="material-icons mr-8pt">error_outline</i>
                                    <div class="media-body"
                                         style="min-width: 180px">
                                        <small class="text-black-100">Currently you haven't created any product categories, please create your product categories</small>
                                    </div>
                                </div>
                            </div>
                        @else
                            <select class="form-control form-select" id="categoryId" name="categoryId" required data-toggle="select">
                                <option disabled selected>Select product's category</option>
                                @foreach($categories AS $category)
                                    <option value="{{ $category->id }}">{{ $category->categoryName }}</option>
                                @endforeach
                            </select>
                            <span class="invalid-feedback categoryId" role="alert">
                                <strong></strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="description">Description</label>
                        <small class="text-sm">(Optional)</small>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        <span class="invalid-feedback description" role="alert">
                            <strong></strong>
                        </span>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="paymentOption">Payment Type</label>
                        <small class="text-danger">*</small>
                        <select class="form-control form-select" id="paymentOption" name="paymentOption" required>
                            <option disabled selected>Select product's payment type</option>
                            @foreach(['subscription', 'one time payment'] AS $payment)
                                <option value="{{ $payment }}">{{ $payment }}</option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback paymentOption" role="alert">
                            <strong></strong>
                        </span>
                    </div>
                    <div class="form-group subscriptionCycleForm">
                        <label class="form-label" for="subscriptionCycle">Subscription Cycle</label>
                        <small class="text-danger">*</small>
                        <select class="form-control form-select" id="subscriptionCycle" name="subscriptionCycle">
                            <option disabled selected>Select product's subscription cycle</option>
                            @foreach(['monthly', 'quarterly', 'semianually', 'anually'] AS $cycle)
                                <option value="{{ $cycle }}">{{ $cycle }}</option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback subscriptionCycle" role="alert">
                            <strong></strong>
                        </span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
