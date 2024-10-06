<!-- Modal add lesson -->
<div class="modal fade" id="addInvoiceModal" tabindex="-1" aria-labelledby="addInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <form method="POST" id="formAddInvoiceModal">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="training-title">Create New Invoice</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="d-flex flex-row align-items-center mb-2">
                            <label class="form-label" for="categoryId">User Contact</label>
                            <small class="text-danger">*</small>
                        </div>
                        <select class="form-control form-select" id="receiverUserId" name="receiverUserId" required data-toggle="select">
                            <option disabled selected>Select users</option>
                            @foreach($contacts AS $contact)
                                <option value="{{ $contact->id }}" data-avatar-src="{{ Storage::url($contact->foto) }}">
                                    {{ $contact->firstName }} {{ $contact->lastName }} ~ {{ $contact->email }}
                                </option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback receiverUserId" role="alert">
                            <strong></strong>
                        </span>
                    </div>

                    <div class="page-separator">
                        <div class="page-separator__text">Product Items</div>
                        <button type="button"  class="btn btn-primary btn-sm ml-auto" id="addProduct">
                            <span class="material-icons mr-2">add</span>
                            Add more
                        </button>
                    </div>
                    <div id="productsField">
                        <div class="row">
                            <div class="col-auto d-flex align-items-center">
                                <label class="form-label"># 1</label>
                            </div>
                            <div class="form-group col-7 col-lg-4">
                                <label class="form-label" for="productId">Product</label>
                                <small class="text-danger">*</small>
                                <select class="form-control form-select product-select" data-row="1" id="productId1" name="products[1][productId]" required>
                                    <option disabled selected>Select product</option>
                                    @foreach($products AS $product)
                                        <option value="{{ $product->id }}">
                                            {{ $product->productName }} ~ {{ $product->priceOption }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback price" role="alert">
                                    <strong></strong>
                                </span>
                            </div>
                            <div class="form-group col-3 col-lg-1">
                                <label class="form-label" for="qty">Qty</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       id="qty1"
                                       data-row="1"
                                       name="products[1][qty]"
                                       required
                                       class="form-control qty-form"
                                       placeholder="Input product's qty ...">
                                <span class="invalid-feedback qty" role="alert">
                                    <strong></strong>
                                </span>
                            </div>
                            <div class="form-group col-6 col-lg-3">
                                <label class="form-label" for="price">Price</label>
                                <div class="input-group input-group-merge">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            Rp.
                                        </div>
                                    </div>
                                    <input type="number"
                                           id="price1"
                                           name="products[1][price]"
                                           class="form-control"
                                           required
                                           disabled>
                                    <span class="invalid-feedback price" role="alert">
                                        <strong></strong>
                                    </span>
                                    <div class="input-group-append">
                                        <div class="input-group-text" id="subscription-info1">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-4 col-lg-2">
                                <label class="form-label" for="amount">Total</label>
                                <div class="input-group input-group-merge">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            Rp.
                                        </div>
                                    </div>
                                    <input type="number"
                                           id="amount1"
                                           name="products[1][ammount]"
                                           class="form-control"
                                           required
                                           disabled>
                                    <span class="invalid-feedback ammount" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="page-separator">
                        <div class="page-separator__text">General Info</div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label class="form-label" for="dueDate">Due Date</label>
                            <small class="text-danger">*</small>
                            <input type="date"
                                   id="dueDate"
                                   name="dueDate"
                                   required
                                   class="form-control"
                                   placeholder="Input invoice's due date ...">
                            <span class="invalid-feedback dueDate" role="alert">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group col-lg-6">
                            <label class="form-label" for="taxId">Tax</label>
                            <small>(Optional)</small>
                            <select class="form-control form-select" id="taxId" name="taxId" required data-toggle="select">
                                <option disabled selected>Select tax</option>
                                @foreach($taxes AS $tax)
                                    <option value="{{ $tax->id }}">
                                        {{ $tax->taxName }} ~ {{ $tax->percentage }}%
                                    </option>
                                @endforeach
                            </select>
                            <span class="invalid-feedback taxId" role="alert">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="list-group-item d-flex flex-column align-items-end">
                    <p class="form-label" id="subtotal"></p>
                    <p class="form-label" id="tax"></p>
                    <p class="form-label" id="totalAmount"></p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
