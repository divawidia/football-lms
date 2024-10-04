<!-- Modal add lesson -->
<div class="modal fade" id="addInvoiceModal" tabindex="-1" aria-labelledby="addInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
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
                                <option value="{{ $contact->id }}" data-avatar-src="{{ Storage::url($player->user->foto) }}">
                                    {{ $contact->firstName }} {{ $contact->lastName }} ~ {{ $contact->roles->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback receiverUserId" role="alert">
                            <strong></strong>
                        </span>
                    </div>

                    <div class="page-separator">
                        <div class="page-separator__text">Product Items</div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-5">
                            <label class="form-label" for="productId">Product</label>
                            <small class="text-danger">*</small>
                            <select class="form-control form-select" id="productId" name="products[1][productId]" required data-toggle="select">
                                <option disabled selected>Select users</option>
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
                        <div class="form-group col-lg-1">
                            <label class="form-label" for="qty">Qty</label>
                            <small class="text-danger">*</small>
                            <input type="number"
                                   id="qty"
                                   name="products[1][qty]"
                                   required
                                   class="form-control"
                                   placeholder="Input product's qty ...">
                            <span class="invalid-feedback" role="alert">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="input-group input-group-merge col-lg-2">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    Rp.
                                </div>
                            </div>
                            <input type="number"
                                   id="price"
                                   name="products[1][price]"
                                   class="form-control"
                                   disabled>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    /Month
                                </div>
                            </div>
                        </div>
                        <div class="input-group input-group-merge col-lg-2">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    Rp.
                                </div>
                            </div>
                            <input type="number"
                                   id="price"
                                   name="products[1][ammount]"
                                   class="form-control"
                                   disabled>
                        </div>
                        <div class="col-lg-2">
                            <button type="button" name="add" id="add" class="btn btn-success"><i class="bx bx-plus"></i></button>
                        </div>
                    </div>

                    <div class="page-separator">
                        <div class="page-separator__text">General Info</div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label class="form-label" for="qty">Due Date</label>
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
                            <label class="form-label" for="productId">Tax</label>
                            <small>(Optional)</small>
                            <select class="form-control form-select" id="taxId" name="taxId" required data-toggle="select">
                                <option disabled selected>Select tax</option>
                                @foreach($taxes AS $tax)
                                    <option value="{{ $tax->id }}">
                                        {{ $tax->taxName }} ~ {{ $tax->percentage }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="invalid-feedback taxId" role="alert">
                                <strong></strong>
                            </span>
                        </div>
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
