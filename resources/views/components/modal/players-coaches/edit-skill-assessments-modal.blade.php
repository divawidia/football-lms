<x-modal.form id="editSkillStatsModal" formId="formEditSkillStatsModal" title="Edit Player Skill Stats" :editForm="true" size="modal-lg">
    <x-forms.basic-input type="hidden" name="matchId" :modal="true"/>
    <x-forms.basic-input type="hidden" name="trainingId" :modal="true"/>
    <x-forms.basic-input type="hidden" name="skillStatsId" :modal="true"/>

    <x-forms.range-slider name="controlling" label="Controlling :" :modal="true"/>
    <x-forms.range-slider name="recieving" label="Receiving :" :modal="true"/>
    <x-forms.range-slider name="dribbling" label="Dribbling :" :modal="true"/>
    <x-forms.range-slider name="passing" label="Passing :" :modal="true"/>
    <x-forms.range-slider name="shooting" label="Shooting :" :modal="true"/>
    <x-forms.range-slider name="crossing" label="Crossing :" :modal="true"/>
    <x-forms.range-slider name="turning" label="Turning :" :modal="true"/>
    <x-forms.range-slider name="ballHandling" label="Ball Handling :" :modal="true"/>
    <x-forms.range-slider name="powerKicking" label="Power Kicking :" :modal="true"/>
    <x-forms.range-slider name="goalKeeping" label="Goal Keeping :" :modal="true"/>
    <x-forms.range-slider name="offensivePlay" label="Offensive Play :" :modal="true"/>
    <x-forms.range-slider name="defensivePlay" label="Defensive Play :" :modal="true"/>
</x-modal.form>

@push('addon-script')
    <script>
        $(document).ready(function (){
            const modalId = '#editSkillStatsModal'
            const formId = "#formEditSkillStatsModal"

            // show add skill stats form modal when update skill button clicked
            $('body').on('click', '.editSkills', function (e) {
                e.preventDefault();
                const matchId = $(this).attr('data-matchId');
                const trainingId = $(this).attr('data-trainingId');
                const skillStatsId = $(this).attr('data-statsId');

                $.ajax({
                    url: "{!! url()->route('skill-assessments.edit', ['skillStats' => ':id']) !!}".replace(':id', skillStatsId),
                    type: 'get',
                    success: function () {
                        $(modalId).modal('show');
                        $(formId+' #matchId').val(matchId);
                        $(formId+' #trainingId').val(trainingId);
                        $(formId+' #skillStatsId').val(skillStatsId);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when creating data!",
                            text: errorThrown,
                        });
                    }
                });
            });

            processModalForm(
                formId,
                "{{ route('skill-assessments.update', ['skillStats' => ':id']) }}",
                "#skillStatsId",
                modalId
            );
        });
    </script>
@endpush
