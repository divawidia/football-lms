<x-modal.form id="createPerformanceReviewModal" formId="formCreatePerformanceReviewModal" title="Add performance review to player">
    <x-forms.basic-input type="hidden" name="playerId" :modal="true"/>

    <x-forms.textarea name="performanceReview" label="Performance Review" placeholder="Input player's performance review here ..." :modal="true"/>
</x-modal.form>

@push('addon-script')
    <script type="module">
        import { processModalForm } from "{{ Vite::asset('resources/js/ajax-processing-data.js') }}";
        import { clearModalFormValidation } from "{{ Vite::asset('resources/js/modal.js') }}";

        $(document).ready(function (){
            const modalId = '#createPerformanceReviewModal';
            const formId = '#formCreateNoteModal';

            $('body').on('click', '.addPerformanceReview',function(e) {
                e.preventDefault();
                const playerId = $(this).attr('id');
                const eventId = $(this).attr('data-eventId');

                $(modalId).modal('show');
                clearModalFormValidation(formId)
                $(formId+' #eventId').val(eventId);
                $(formId+' #playerId').val(playerId);
            });
            processModalForm(
                formId,
                "{{ route('coach.performance-reviews.store', ['player'=> ':id']) }}",
                "#playerId",
                modalId
            );
        });
    </script>
@endpush
