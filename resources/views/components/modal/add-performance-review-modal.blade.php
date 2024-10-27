<div class="modal fade" id="createPerformanceReviewModal" tabindex="-1" aria-labelledby="createPerformanceReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ $routeCreate }}" method="post" id="formCreatePerformanceReviewModal">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="playerName">Add performance review to player</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="eventId" name="eventId"/>
                    <input type="hidden" id="playerId"/>
                    <div class="form-group">
                        <label class="form-label" for="add_performanceReview">Note</label>
                        <small class="text-danger">*</small>
                        <textarea class="form-control" id="add_performanceReview" name="performanceReview" placeholder="Input player's performance review here ..." required>{{ old('note') }}</textarea>
                        <span class="invalid-feedback note_error" role="alert">
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
            $('body').on('click', '.addPerformanceReview',function(e) {
                e.preventDefault();
                const playerId = $(this).attr('id');
                const eventId = $(this).attr('data-eventId');

                $('#createPerformanceReviewModal').modal('show');
                $('#eventId').val(eventId);
                $('#playerId').val(playerId);
            });

            // create schedule note data
            $('#formCreatePerformanceReviewModal').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    method: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        $('#createPerformanceReviewModal').modal('hide');
                        Swal.fire({
                            title: 'Performance review successfully added!',
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
                            $("#add_" + key).addClass('is-invalid');
                        });
                    }
                });
            });
        });
    </script>
@endpush
