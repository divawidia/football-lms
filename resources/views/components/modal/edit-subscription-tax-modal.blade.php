<div class="modal fade" id="editTaxModal" tabindex="-1" aria-labelledby="editTaxModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="post" id="formEditTaxModal">
                @method('PUT')
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="editTaxTitle"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="subscriptionId">
                    <div class="form-group">
                        <label class="form-label" for="taxId">Include Tax</label>
                        <small>(Optional)</small>
                        <select class="form-control form-select" id="edit_taxId" name="taxId" required data-toggle="select">
                            <option disabled selected>Select tax</option>
                        </select>
                        <span class="invalid-feedback taxId_error" role="alert">
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

@push('addon-script')
    <script>
        $(document).ready(function (){
            const body = $('body');

            body.on('click', '.edit-tax', function(e) {
                e.preventDefault();
                const id = $(this).attr('id');

                $.ajax({
                    url: "{{ route('subscriptions.show', ':id') }}".replace(':id', id),
                    type: 'get',
                    success: function(res) {
                        $('#editTaxModal').modal('show');
                        $('#editTaxTitle').text('Edit '+res.data.subscription.user.firstName+' '+res.data.subscription.user.lastName+' subscription of '+res.data.subscription.product.productName+"'s tax");
                        $.each(res.data.taxes, function (key, value) {
                            $('#edit_taxId').append('<option value="' + value.id + '">' + value.taxName + '</option>');
                        });
                        $('#edit_taxId option[value="' + res.data.subscription.taxId + '"]').attr('selected', 'selected');
                        $('#subscriptionId').val(res.data.subscription.id);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when deleting data!",
                            text: errorThrown,
                        });
                    }
                });
            });

            // update schedule note data
            $('#formEditTaxModal').on('submit', function(e) {
                e.preventDefault();
                const id = $('#subscriptionId').val();

                $.ajax({
                    url: "{{ route('subscriptions.update-tax', ['subscription' => ":id"]) }}".replace(':id', id),
                    type: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function() {
                        $('#editTaxModal').modal('hide');
                        Swal.fire({
                            title: "Player's subscription tax successfully updated!",
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
                    error: function(xhr) {
                        const response = JSON.parse(xhr.responseText);
                        console.log(response);
                        $.each(response.errors, function(key, val) {
                            $('span.' + key + '_error').text(val[0]);
                            $("#edit_" + key).addClass('is-invalid');
                        });
                    }
                });
            });
        });
    </script>
@endpush
