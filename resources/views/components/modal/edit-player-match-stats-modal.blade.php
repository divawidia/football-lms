<div class="modal fade" id="playerMatchStatsModal" tabindex="-1" aria-labelledby="playerMatchStatsModal"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="POST" id="formPlayerMatchStats">
                @method('PUT')
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="playerStatsName"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <input type="hidden" id="playerStatsId">
                            <div class="form-group">
                                <label class="form-label" for="minutesPlayed">Minutes Played</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="minutesPlayed"
                                       name="minutesPlayed"
                                       min="0"
                                       placeholder="Input team own goal">
                                <span class="invalid-feedback minutesPlayed_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="shots">Shots</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="shots"
                                       name="shots"
                                       min="0"
                                       placeholder="Input team Shot">
                                <span class="invalid-feedback shots_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="passes">Passes</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="passes"
                                       name="passes"
                                       min="0"
                                       placeholder="Input team Touches">
                                <span class="invalid-feedback passes_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="fouls">Fouls</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="fouls"
                                       name="fouls"
                                       min="0"
                                       placeholder="Input team Passes">
                                <span class="invalid-feedback fouls_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label" for="yellowCards">Yellow Cards</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="yellowCards"
                                       name="yellowCards"
                                       min="0"
                                       placeholder="Input team Tackles">
                                <span class="invalid-feedback yellowCards_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="redCards">Red Cards</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="redCards"
                                       name="redCards"
                                       min="0"
                                       placeholder="Input team Clearances">
                                <span class="invalid-feedback redCards_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="saves">Saves</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="saves"
                                       name="saves"
                                       min="0"
                                       placeholder="Input team Corners">
                                <span class="invalid-feedback saves_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                        </div>
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

{{-- update player match stats data --}}
<x-modal-form-update-processing formId="#formPlayerMatchStats"
                                updateDataId="#formPlayerMatchStats #playerStatsId"
                                :routeUpdate="route('match-schedules.player-match-stats.update', ['match' => $match->id, 'player' => ':id'])"
                                modalId="#playerMatchStatsModal"/>

@push('addon-script')
    <script>
        $(document).ready(function (){
            const formId = '#formPlayerMatchStats';

            // show create team scorer modal
            $('body').on('click', '.edit-player-stats', function (e) {
                e.preventDefault();
                const id = $(this).attr('id');

                $('#playerMatchStatsModal').modal('show');
                $(formId+' .invalid-feedback').empty();
                $(formId+' input').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('match-schedules.player-match-stats.show', ['match' => $match->id, 'player' => ":id"]) }}".replace(':id', id),
                    type: 'GET',
                    success: function (res) {
                        $('#playerStatsName').text('Update Player ' + res.data.playerData.firstName + ' ' + res.data.playerData.lastName + ' Stats');
                        $('#playerStatsId').val(res.data.statsData.playerId);
                        $.each(res.data.statsData, function (key, val) {
                            $('#' + key).val(val);
                        });
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
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
