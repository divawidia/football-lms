<x-modal.form id="teamMatchStatsModal" formId="formTeamMatchStats" :editForm="true">
    <x-forms.basic-input type="hidden" name="teamId" :modal="true" :required="false"/>
    <x-forms.basic-input type="hidden" name="teamSide" :modal="true"/>
    <div class="row">
        <div class="col-6">
            <x-forms.basic-input type="number" min="0" max="100" name="teamPossesion" label="Possession" placeholder="Input team's possession" :modal="true" :required="false"/>

            <x-forms.basic-input type="number" min="0" name="teamShotOnTarget" label="Show on Target" placeholder="Input team's shot on target" :modal="true" :required="false"/>

            <x-forms.basic-input type="number" min="0" name="teamShots" label="Shots" placeholder="Input team's shots" :modal="true" :required="false"/>

            <x-forms.basic-input type="number" min="0" name="teamTouches" label="Touches" placeholder="Input team's touches" :modal="true" :required="false"/>

            <x-forms.basic-input type="number" min="0" name="teamPasses" label="Passes" placeholder="Input team's passes" :modal="true" :required="false"/>

            <x-forms.basic-input type="number" min="0" name="teamTackles" label="Tackles" placeholder="Input team's tackles" :modal="true" :required="false"/>
        </div>
        <div class="col-6">
            <x-forms.basic-input type="number" min="0" name="teamClearances" label="Clearances" placeholder="Input team's clearances" :modal="true" :required="false"/>

            <x-forms.basic-input type="number" min="0" name="teamCorners" label="Corners" placeholder="Input team's corners" :modal="true" :required="false"/>

            <x-forms.basic-input type="number" min="0" name="teamOffsides" label="Offsides" placeholder="Input team's offsides" :modal="true" :required="false"/>

            <x-forms.basic-input type="number" min="0" name="teamYellowCards" label="Yellow Cards" placeholder="Input team's yellow cards" :modal="true" :required="false"/>

            <x-forms.basic-input type="number" min="0" name="teamRedCards" label="Red Cards" placeholder="Input team's red cards" :modal="true" :required="false"/>

            <x-forms.basic-input type="number" min="0" name="teamFoulsConceded" label="Fouls Conceded" placeholder="Input team's fouls conceded" :modal="true" :required="false"/>
        </div>
    </div>
</x-modal.form>

@push('addon-script')
    <script>
        $(document).ready(function (){
            const formId = '#formTeamMatchStats';
            const modalId = '#teamMatchStatsModal';
            const btnClass = '.update-team-match-stats-btn';
            let team

            $(btnClass).on('click', function(e) {
                e.preventDefault();
                $(modalId).modal('show');
                team = $(this).attr('id');
                clearModalFormValidation(formId)

                $.ajax({
                    url: "{{ route('match-schedules.match-stats', ['schedule' => $match->hash]) }}",
                    type: 'GET',
                    data: {
                        team: team,
                    },
                    dataType: 'JSON',
                    success: function (result) {
                        $(formId+' #teamSide').val(team)
                        $(formId+' .modal-title').text('Update '+result.data.teamName+' Match Stats')

                        if (team === 'externalTeam') {
                            $.each(result.data, function (key, value) {
                                $(formId + ' #' + key).val(value)
                            });
                        } else {
                            $(formId+' #teamId').val(result.data.id)
                            $.each(result.data.pivot, function (key, value) {
                                $(formId+' #'+key).val(value)
                            });
                        }
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
                "{{ route('match-schedules.update-match-stats', $match->hash) }}",
                null,
                modalId
            );
        });
    </script>
@endpush
