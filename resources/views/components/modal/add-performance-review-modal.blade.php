<div class="modal fade" id="createPerformanceReviewModal" tabindex="-1" aria-labelledby="createPerformanceReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" id="formCreatePerformanceReviewModal">
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

<x-modal-form-update-processing formId="#formCreatePerformanceReviewModal"
                                updateDataId="#formCreatePerformanceReviewModal #playerId"
                                :routeUpdate="$routeCreate"
                                modalId="#createPerformanceReviewModal"/>
@push('addon-script')
    <script>
        $(document).ready(function (){
            const modalId = '#createPerformanceReviewModal';

            $('body').on('click', '.addPerformanceReview',function(e) {
                e.preventDefault();
                const playerId = $(this).attr('id');
                const eventId = $(this).attr('data-eventId');

                $(modalId).modal('show');
                $(modalId+' #eventId').val(eventId);
                $(modalId+' #playerId').val(playerId);
            });
        });
    </script>
@endpush
