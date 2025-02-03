<x-modal.form id="createPerformanceReviewModal" formId="formCreatePerformanceReviewModal" title="Add performance review to player" :editForm="false" size="">
    <x-forms.basic-input type="hidden" name="playerId" :modal="true"/>
    <x-forms.basic-input type="hidden" name="matchId" :modal="true"/>
    <x-forms.basic-input type="hidden" name="trainingId" :modal="true"/>

    <x-forms.textarea name="performanceReview" label="Performance Review" placeholder="Input player's performance review here ..." :modal="true"/>
</x-modal.form>

@push('addon-script')
    <script>
        $(document).ready(function (){
            const modalId = '#createPerformanceReviewModal';
            const formId = '#formCreatePerformanceReviewModal';

            $('body').on('click', '.addPerformanceReview',function(e) {
                e.preventDefault();
                const playerId = $(this).attr('id');
                const matchId = $(this).attr('data-matchId');
                const trainingId = $(this).attr('data-trainingId');

                $(modalId).modal('show');
                clearModalFormValidation(formId)
                $(formId+' #matchId').val(matchId);
                $(formId+' #trainingId').val(trainingId);
                $(formId+' #playerId').val(playerId);
            });
            processModalForm(
                formId,
                "{{ route('player-managements.performance-reviews.store', ['player'=> ':id']) }}",
                "#playerId",
                modalId
            );
        });
    </script>
@endpush
