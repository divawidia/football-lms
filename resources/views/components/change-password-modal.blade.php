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

@push('addon-script')
    <script>
        $(document).ready(function () {
            @if(isAllAdmin())
            $('body').on('click', '.changePassword', function (e) {
                const id = $(this).attr('id');
                e.preventDefault();
                $('#changePasswordModal').modal('show');
                $('#userId').val(id);
            })

            // update admin password
            $('#formChangePasswordModal').on('submit', function (e) {
                e.preventDefault();
                const id = $('#userId').val();
                $.ajax({
                    url: "{{ $route }}".replace(':id', id),
                    type: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function () {
                        $('#changePasswordModal').modal('hide');
                        Swal.fire({
                            title: 'Accounts password successfully updated!',
                            icon: 'success',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: "#1ac2a1",
                            confirmButtonText:
                                'Ok!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    },
                    error: function (xhr) {
                        const response = JSON.parse(xhr.responseText);
                        console.log(response);
                        $.each(response.errors, function (key, val) {
                            $('span.' + key + '_error').text(val[0]);
                            $("#add_" + key).addClass('is-invalid');
                        });
                    }
                });
            });
            @endif
        });
    </script>
@endpush
