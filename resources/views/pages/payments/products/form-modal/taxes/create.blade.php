<!-- Modal add lesson -->
<div class="modal fade" id="addTaxModal" tabindex="-1" aria-labelledby="addTaxMOdalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="formAddTaxModal">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="training-title">Create new tax</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-7">
                            <label class="form-label" for="taxName">Tax Name</label>
                            <small class="text-danger">*</small>
                            <input type="text"
                                   id="taxName"
                                   name="taxName"
                                   class="form-control"
                                   placeholder="Input tax's name ..."
                                   required>
                            <span class="invalid-feedback taxName" role="alert">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group col-lg-5">
                            <label class="form-label" for="percentage">Tax Percentage</label>
                            <small class="text-danger">*</small>
                            <div class="input-group input-group-merge">
                                <input type="number"
                                       id="percentage"
                                       name="percentage"
                                       class="form-control"
                                       placeholder="Input tax's percentage ..."
                                       required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        %
                                    </div>
                                </div>
                                <span class="invalid-feedback percentage" role="alert">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>
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
