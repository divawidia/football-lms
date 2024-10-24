<!-- Modal edit player attendance -->
<div class="modal fade" id="changePasswordModal" tabindex="-1"
     aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="post" id="formChangePasswordModal">
                @method('PATCH')
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Change Account's Password</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="userId">
                    <div class="form-group">
                        <label class="form-label" for="add_password">New Password</label>
                        <small class="text-danger">*</small>
                        <input type="password"
                               class="form-control"
                               id="add_password"
                               name="password"
                               required
                               placeholder="Input account's new password ...">
                        <span class="invalid-feedback password_error" role="alert">
                            <strong></strong>
                        </span>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="add_password-confirm">Confirm Password</label>
                        <small class="text-danger">*</small>
                        <input type="password"
                               class="form-control"
                               name="password_confirmation" required id="add_password-confirm"
                               placeholder="Retype inputted password ...">
                    </div>
                    <span class="invalid-feedback password-confirm_error" role="alert">
                        <strong></strong>
                    </span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
