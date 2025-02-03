<x-modal.form id="addSkillStatsModal" formId="formAddSkillStatsModal" title="Update Player Skill Stats" size="modal-lg" :editForm="false">
    <x-forms.basic-input type="hidden" name="matchId" :modal="true"/>
    <x-forms.basic-input type="hidden" name="trainingId" :modal="true"/>
    <x-forms.basic-input type="hidden" name="playerId" :modal="true"/>
    <x-forms.range-slider name="controlling" label="controlling :" :modal="true"/>
    <x-forms.range-slider name="recieving" label="recieving ball :" :modal="true"/>
    <x-forms.range-slider name="dribbling" label="dribbling :" :modal="true"/>
    <x-forms.range-slider name="passing" label="passing :" :modal="true"/>
    <x-forms.range-slider name="shooting" label="shooting :" :modal="true"/>
    <x-forms.range-slider name="crossing" label="crossing :" :modal="true"/>
    <x-forms.range-slider name="turning" label="turning :" :modal="true"/>
    <x-forms.range-slider name="ballHandling" label="ball Handling :" :modal="true"/>
    <x-forms.range-slider name="powerKicking" label="power Kicking :" :modal="true"/>
    <x-forms.range-slider name="goalKeeping" label="goal Keeping :" :modal="true"/>
    <x-forms.range-slider name="offensivePlay" label="offensive Play :" :modal="true"/>
    <x-forms.range-slider name="defensivePlay" label="defensive Play :" :modal="true"/>
</x-modal.form>

@push('addon-script')

    <script>
        $(document).ready(function (){
            const formId = '#formAddSkillStatsModal';
            const modalId = '#addSkillStatsModal';

            // show add skill stats form modal when update skill button clicked
            $('body').on('click', '.addSkills', function (e) {
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
                "{{ route('skill-assessments.store', ['player' => ':id']) }}",
                '#playerId',
                modalId
            );
        });
    </script>
@endpush
