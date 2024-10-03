<!-- Modal add lesson -->
<div class="modal fade" id="addProductCategoryModal" tabindex="-1" aria-labelledby="addProductCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="formAddProductCategoryModal">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="training-title">Create new product category</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="categoryName">Product Category Name</label>
                        <small class="text-danger">*</small>
                        <input type="text"
                               id="categoryName"
                               name="categoryName"
                               class="form-control"
                               placeholder="Input product category's name ..."
                               required>
                        <span class="invalid-feedback categoryName" role="alert">
                            <strong></strong>
                        </span>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="description">Description</label>
                        <small class="text-sm">(Optional)</small>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        <span class="invalid-feedback description" role="alert">
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
