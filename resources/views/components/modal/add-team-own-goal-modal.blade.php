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
                    <div class="form-group">
                        <label class="form-label" for="playerId">Player Name</label>
                        <small class="text-danger">*</small>
                        <select class="form-control form-select" id="playerId" name="playerId" required></select>
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
            $('#addOwnGoal').on('click', function (e) {
                e.preventDefault();
                $('#createTeamOwnGoalModal').modal('show');
                $(formId+' .invalid-feedback').empty();
                $('select').removeClass('is-invalid');
                $('input').removeClass('is-invalid');

                $.ajax({
                    url: "{{route('get-event-player', ['schedule' => $eventSchedule->id]) }}",
                    type: 'GET',
                    dataType: 'json',
                    success: function (result) {
                        $(formId+' #playerId').html('<option disabled selected>Select player who scored the own goal</option>');
                        $.each(result.data, function (key, value) {
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
        });
    </script>
@endpush
