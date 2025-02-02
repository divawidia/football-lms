<x-modal.form id="createTeamScorerModal" formId="formAddScorerModal">
    <x-forms.basic-input type="hidden" name="teamId" :modal="true"/>
    <x-forms.basic-input type="hidden" name="dataTeam" :modal="true"/>

    <x-forms.select name="playerId" label="Player Name" :modal="true" :select2="false"></x-forms.select>

    <x-forms.select name="assistPlayerId" label="Assist Player Name" :modal="true" :select2="false"></x-forms.select>

    <x-forms.basic-input type="number" min="1" max="160" name="minuteScored" label="Minutes Scored" placeholder="Input minutes the player scored the goal, E.g. : 60" :modal="true"/>
</x-modal.form>

@push('addon-script')
    <script>
        $(document).ready(function (){
            const formId = '#formAddScorerModal';
            const modalId = '#createTeamScorerModal';
            const btnClass = '.addTeamScorer';
            let team;

            // show create team scorer
            $(btnClass).on('click', function(e) {
                e.preventDefault();
                $(modalId).modal('show');
                team = $(this).attr('id')
                clearModalFormValidation(formId)
                $(formId+' #assistPlayerId').empty()

                $.ajax({
                    url: "{{ route('match-schedules.players', ['match' => $match->hash]) }}",
                    type: 'GET',
                    data: {
                        team: team,
                    },
                    dataType: 'JSON',
                    success: function (result) {
                        $(formId+' .modal-title').text('Add '+result.data.team.teamName+' Match Scorer')
                        $(formId+' #teamId').val(result.data.team.id)
                        $(formId+' #dataTeam').val(team)

                        $(formId+' #playerId').html('<option disabled selected>Select player who scored the goal</option>');
                        $.each(result.data.players, function (key, value) {
                            {{--$(formId+' #playerId').append('<option value="' + value.id + '" data-avatar-src={{ Storage::url('') }}' + value.user.foto + '>' + value.user.firstName + ' ' + value.user.lastName + ' ~ ' + value.position.name + '</option>');--}}
                            $(formId+' #playerId').append('<option value="' + value.id + '">' + value.user.firstName + ' ' + value.user.lastName + ' ~ ' + value.position.name + '</option>');
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

            // fetch assist player data
            $(formId+' #playerId').on('change', function () {
                const id = $(this).val()

                $.ajax({
                    url: "{{ route('match-schedules.players', ['match' => $match->hash]) }}",
                    type: 'GET',
                    data: {
                        team: team,
                        exceptPlayerId: id,
                    },
                    dataType: 'JSON',
                    success: function (result) {
                        $(formId+' #assistPlayerId').html('<option disabled selected>Select player who assisted the goal</option>');
                        $.each(result.data.players, function (key, value) {
                            {{--$(formId+' #assistPlayerId').append('<option value="' + value.id + '" data-avatar-src={{ Storage::url('') }}' + value.user.foto + '>' + value.user.firstName + ' ' + value.user.lastName + ' ~ ' + value.position.name + '</option>');--}}
                            $(formId+' #assistPlayerId').append('<option value="' + value.id + '">' + value.user.firstName + ' ' + value.user.lastName + ' ~ ' + value.position.name + '</option>');
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
                "{{ route('match-schedules.store-match-scorer', $match->hash) }}",
                null,
                modalId
            );
        });
    </script>
@endpush
