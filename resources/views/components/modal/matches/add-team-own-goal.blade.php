<x-modal.form id="createTeamOwnGoalModal" formId="formAddOwnGoal">
    <x-forms.basic-input type="hidden" name="teamId" :modal="true"/>
    <x-forms.basic-input type="hidden" name="dataTeam" :modal="true"/>

    <x-forms.select name="playerId" label="Player Name" :modal="true" :select2="false"></x-forms.select>

    <x-forms.basic-input type="number" min="1" max="160" name="minuteScored" label="Minutes Scored" placeholder="Input minutes the player scored the own goal, E.g. : 60" :modal="true"/>
</x-modal.form>

@push('addon-script')
    <script type="module">
        import { processModalForm } from "{{ Vite::asset('resources/js/ajax-processing-data.js') }}" ;
        import { clearModalFormValidation } from "{{ Vite::asset('resources/js/modal.js') }}";

        $(document).ready(function (){
            const formId = '#formAddOwnGoal';
            const modalId = '#createTeamOwnGoalModal';
            const btnClass = '.addOwnGoal';
            let team;

            $(btnClass).on('click', function(e) {
                e.preventDefault();
                $(modalId).modal('show');
                team = $(this).attr('id')
                clearModalFormValidation(formId)

                $.ajax({
                    url: "{{ route('match-schedules.players', ['schedule' => $eventSchedule->hash]) }}",
                    type: 'GET',
                    data: {
                        team: team,
                    },
                    dataType: 'JSON',
                    success: function (result) {
                        $(formId+' .modal-title').text('Add '+result.data.team.teamName+' Match Own Goal')
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

            processModalForm(
                formId,
                "{{ route('match-schedules.store-own-goal', $eventSchedule->hash) }}",
                null,
                modalId
            );
        });
    </script>
@endpush
