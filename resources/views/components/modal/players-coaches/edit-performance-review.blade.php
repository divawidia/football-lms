<x-modal.form id="editPerformanceReviewModal" formId="formEditPerformanceReviewModal" title="" :editForm="true" size="">

    <x-forms.basic-input type="hidden" name="eventId" :modal="true"/>

    <x-forms.basic-input type="hidden" name="reviewId" :modal="true"/>

    <x-forms.textarea name="performanceReview" label="Performance Review" placeholder="Input player's performance review here ..." :modal="true"/>

</x-modal.form>

@push('addon-script')
    <script>
        $(document).ready(function (){
            const formId = '#formEditPerformanceReviewModal'
            const modalId = '#editPerformanceReviewModal';

            $('body').on('click', '.editPerformanceReview',function(e) {
                e.preventDefault();
                const reviewId = $(this).attr('data-reviewId');
                const matchId = $(this).attr('data-matchId');
                const trainingId = $(this).attr('data-trainingId');

                $.ajax({
                    url: "{!! url()->route('player-managements.performance-reviews.edit', ['review' => ':id']) !!}".replace(':id', reviewId),
                    type: 'get',
                    success: function (res) {
                        $(modalId).modal('show');
                        clearModalFormValidation(formId)

                        $(formId+' #matchId').val(matchId);
                        $(formId+' #trainingId').val(trainingId);
                        $(formId+' #reviewId').val(reviewId);
                        $(formId+' .modal-title').text("Edit "+res.data.player.firstName+" "+res.data.player.lastName+"'s performance review");
                        $(formId+' #performanceReview').text(res.data.review.performanceReview);
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

            processModalForm(
                formId,
                "{{ route('player-managements.performance-reviews.update', ['review' => ':id']) }}",
                "#reviewId",
                modalId
            );
        });
    </script>
@endpush
