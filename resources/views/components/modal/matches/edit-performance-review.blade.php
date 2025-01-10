<div class="modal fade" id="editPerformanceReviewModal" tabindex="-1" aria-labelledby="editPerformanceReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" id="formEditPerformanceReviewModal">
                @method('PUT')
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="playerName">Edit player's performance review</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="eventId" name="eventId"/>
                    <input type="hidden" id="reviewId"/>
                    <div class="form-group">
                        <label class="form-label" for="performanceReview">Performance Review</label>
                        <small class="text-danger">*</small>
                        <textarea class="form-control" id="performanceReview" name="performanceReview" placeholder="Input player's performance review here ..." required rows="10"></textarea>
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

<x-modal-form-update-processing formId="#formEditPerformanceReviewModal"
                                updateDataId="#editPerformanceReviewModal #reviewId"
                                :routeUpdate="route('coach.performance-reviews.update', ['review' => ':id'])"
                                modalId="#editPerformanceReviewModal"/>
@push('addon-script')
    <script>
        $(document).ready(function (){
            const modalId = '#editPerformanceReviewModal';

            $('body').on('click', '.editPerformanceReview',function(e) {
                e.preventDefault();
                const reviewId = $(this).attr('data-reviewId');
                const eventId = $(this).attr('data-eventId');

                $.ajax({
                    url: "{!! url()->route('coach.performance-reviews.edit', ['review' => ':id']) !!}".replace(':id', reviewId),
                    type: 'get',
                    success: function (res) {
                        $(modalId).modal('show');
                        $(modalId+' #eventId').val(eventId);
                        $(modalId+' #reviewId').val(reviewId);
                        $(modalId+' #performanceReview').text(res.data.performanceReview);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when retrieving data!",
                            text: errorThrown,
                        });
                    }
                });
            });
        });
    </script>
@endpush
