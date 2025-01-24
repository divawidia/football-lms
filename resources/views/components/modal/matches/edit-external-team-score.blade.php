<x-modal.form id="editExternalTeamScoreModal" formId="formEditExternalTeamScore" :editForm="true">
    <x-forms.basic-input type="hidden" name="teamSide" :modal="true"/>
    <div class="row">
        <div class="col-6">
            <x-forms.basic-input type="number" min="0" name="goalScored" label="Goal Scored" placeholder="Input team's goal scored" :modal="true"/>
        </div>
        <div class="col-6">
            <x-forms.basic-input type="number" min="0" name="teamOwnGoal" label="Own Goal" placeholder="Input team's own goal" :modal="true" :required="false"/>
        </div>
    </div>
</x-modal.form>

@push('addon-script')
    <script>
        $(document).ready(function (){
            const formId = '#formEditExternalTeamScore';
            const modalId = '#editExternalTeamScoreModal';

            $('.edit-team-score-btn').on('click', function(e) {
                e.preventDefault();
                $(modalId).modal('show');
                clearModalFormValidation(formId)

                $.ajax({
                    url: "{{ route('match-schedules.match-stats', ['schedule' => $match->hash]) }}",
                    type: 'GET',
                    dataType: 'JSON',
                    success: function (result) {
                        $(formId+' .modal-title').text('Update '+result.data.teamName+' Team Score')
                        $.each(result.data, function (key, value) {
                            $(formId + ' #' + key).val(value)
                        });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        const response = JSON.parse(jqXHR.responseText);
                        Swal.fire({
                            icon: "error",
                            title: errorThrown,
                            text: response.message,
                        });
                    }
                });
            });

            processModalForm(
                formId,
                "{{ route('match-schedules.update-external-team-score', $match->hash) }}",
                null,
                modalId
            );
        });
    </script>
@endpush
