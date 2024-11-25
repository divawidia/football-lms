<div class="modal fade" id="createTeamScorerModal" tabindex="-1" aria-labelledby="createTeamScorerModal"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="POST" id="formAddScorerModal">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add team scorer of this match</h5>
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
                        <label class="form-label" for="assistPlayerId">Assist Player Name</label>
                        <small class="text-danger">*</small>
                        <select class="form-control form-select" id="assistPlayerId" name="assistPlayerId" required></select>
                        <span class="invalid-feedback assistPlayerId_error" role="alert"><strong></strong></span>
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
                               placeholder="Pick minutes the player scored the goal. Eg : 60"
                               required>
                        <span class="invalid-feedback minuteScored_error" role="alert"><strong></strong></span>
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

<x-modal-form-update-processing formId="#formAddScorerModal"
                                updateDataId=""
                                :routeUpdate="route('match-schedules.store-match-scorer', $eventSchedule->id)"
                                modalId="#createTeamScorerModal"/>

@push('addon-script')
    <script>
        $(document).ready(function (){
            const formId = '#formAddScorerModal';

            // show create team scorer modal
            $('#addTeamScorer').on('click', function (e) {
                e.preventDefault();
                $('#createTeamScorerModal').modal('show');
                $(formId+' .invalid-feedback').empty();
                $('select').removeClass('is-invalid');
                $('input').removeClass('is-invalid');

                $.ajax({
                    url: "{{route('get-event-player', ['schedule' => $eventSchedule->id]) }}",
                    type: 'GET',
                    dataType: 'json',
                    success: function (result) {
                        $(formId+' #playerId').html('<option disabled selected>Select player who scored the goal</option>');
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

            // fetch assist player data
            $(formId+' #playerId').on('change', function () {
                const id = this.value;

                $.ajax({
                    url: "{{route('get-assist-player', ['schedule' => $eventSchedule->id,'player'=>':id']) }}".replace(':id', id),
                    type: 'GET',
                    dataType: 'json',
                    success: function (result) {
                        $(formId+' #assistPlayerId').html('<option disabled selected>Select player who assisted the goal</option>');
                        $.each(result.data, function (key, value) {
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
        });
    </script>
@endpush
