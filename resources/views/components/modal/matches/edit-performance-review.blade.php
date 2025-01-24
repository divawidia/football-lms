<x-modal.form id="editPerformanceReviewModal" formId="formEditPerformanceReviewModal" :editForm="true">

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
                const eventId = $(this).attr('data-eventId');

                $.ajax({
                    url: "{!! url()->route('coach.performance-reviews.edit', ['review' => ':id']) !!}".replace(':id', reviewId),
                    type: 'get',
                    success: function (res) {
                        $(modalId).modal('show');
                        clearModalFormValidation(formId)

                        $(formId+' #eventId').val(eventId);
                        $(formId+' #reviewId').val(reviewId);
                        $(formId+' .modal-title').text(res.data.performanceReview);
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
                "{{ route('coach.performance-reviews.update', ['review' => ':id']) }}",
                "#reviewId",
                modalId
            );
        });
    </script>
@endpush
