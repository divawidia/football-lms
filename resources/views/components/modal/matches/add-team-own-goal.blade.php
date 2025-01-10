<div class="modal fade" id="createTeamOwnGoalModal" tabindex="-1" aria-labelledby="createTeamOwnGoalModal"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="post" id="formAddOwnGoal">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="coachName">Add own goal of this match</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="dataTeam" name="dataTeam">
                    <input type="hidden" id="teamId" name="teamId">
                    <div class="form-group">
                        <label class="form-label" for="playerId">Player Name</label>
                        <small class="text-danger">*</small>
                        <select class="form-control form-select" id="playerId" name="playerId" data-toggle="select" required></select>
                        <span class="invalid-feedback playerId_error" role="alert"><strong></strong></span>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="minuteScored">Minute Scored</label>
                        <small class="text-danger">*</small>
                        <input type="number"
                               class="form-control"
                               id="minuteScored"
                               name="minuteScored"
                               min="1"
                               max="160"
                               placeholder="Pick minutes the player scored the own goal. Example : 60">
                        <span class="invalid-feedback minuteScored_error" role="alert">
                                <strong></strong>
                            </span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- store team own goal data --}}
<x-modal-form-update-processing formId="#formAddOwnGoal"
                                updateDataId=""
                                :routeUpdate="route('match-schedules.store-own-goal', $eventSchedule->id)"
                                modalId="#createTeamOwnGoalModal"/>

@push('addon-script')
    <script>
        $(document).ready(function (){
            const formId = '#formAddOwnGoal';

            // show add own goal modal
            $('.addOwnGoal').on('click', function (e) {
                e.preventDefault();
                const team = $(this).attr('data-team')

                $('#createTeamOwnGoalModal').modal('show');
                $(formId+' .invalid-feedback').empty();
                $(formId+' select').removeClass('is-invalid');
                $(formId+' input').removeClass('is-invalid');

                $.ajax({
                    url: "{{route('get-event-player', ['schedule' => $eventSchedule->id]) }}",
                    type: 'GET',
                    data: {
                        team: team,
                    },
                    dataType: 'json',
                    success: function (result) {
                        $(formId+' .modal-title').text('Add '+result.data.team.teamName+' Match Own Goal')
                        $(formId+' #dataTeam').val(team)
                        $(formId+' #teamId').val(result.data.team.id)
                        $(formId+' #playerId').html('<option disabled selected>Select player who scored the own goal</option>');
                        $.each(result.data.players, function (key, value) {
                            $(formId+' #playerId').append('<option value="' + value.id + '" data-avatar-src={{ Storage::url('') }}' + value.user.foto + '>' + value.user.firstName + ' ' + value.user.lastName + ' ~ ' + value.position.name + '</option>');
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
        });
    </script>
@endpush
