<div class="modal fade" id="teamMatchStatsModal" tabindex="-1" aria-labelledby="teamMatchStatsModal"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="" method="post" id="formTeamMatchStats">
                @method('PUT')
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="coachName">Update team stats of this match</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- TEAM A -->
                    <div class="page-separator">
                        <img src="{{ Storage::url($eventSchedule->teams[0]->logo) }}"
                             width="50"
                             height="50"
                             class="rounded-circle img-object-fit-cover mr-3"
                             alt="team-logo">
                        <div class="page-separator__text">{{ $eventSchedule->teams[0]->teamName }} Match
                            Stats
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-3">
                            @if($eventSchedule->isOpponentTeamMatch == 1)
                                <div class="form-group">
                                    <label class="form-label" for="teamATeamScore">Team Score</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamATeamScore"
                                           name="teamATeamScore"
                                           min="0"
                                           max="100"
                                           value="{{ old('teamATeamScore', $eventSchedule->teams[0]->pivot->teamScore) }}"
                                           placeholder="Input team Team Score">
                                    <span class="invalid-feedback teamATeamScore_error" role="alert">
                                            <strong></strong>
                                        </span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="teamAOwnGoal">Own Goal</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamAOwnGoal"
                                           name="teamAOwnGoal"
                                           min="0"
                                           value="{{ old('teamAOwnGoal', $eventSchedule->teams[0]->pivot->teamOwnGoal) }}"
                                           placeholder="Input team own goal">
                                    <span class="invalid-feedback teamAOwnGoal_error" role="alert">
                                            <strong></strong>
                                        </span>
                                </div>
                            @endif
                            <div class="form-group">
                                <label class="form-label" for="teamAPossession">Possession</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="teamAPossession"
                                       name="teamAPossession"
                                       min="0"
                                       max="100"
                                       required
                                       value="{{ old('teamAPossession', $eventSchedule->teams[0]->pivot->teamPossesion) }}"
                                       placeholder="Input team possession">
                                <span class="invalid-feedback teamAPossession_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="teamAShotOnTarget">Shot on Target</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="teamAShotOnTarget"
                                       name="teamAShotOnTarget"
                                       min="0"
                                       required
                                       value="{{ old('teamAShotOnTarget', $eventSchedule->teams[0]->pivot->teamShotOnTarget) }}"
                                       placeholder="Input team Shot on Target">
                                <span class="invalid-feedback teamAShotOnTarget_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                            @if($eventSchedule->isOpponentTeamMatch == 0)
                                <div class="form-group">
                                    <label class="form-label" for="teamAShots">Shots</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamAShots"
                                           name="teamAShots"
                                           min="0"
                                           required
                                           value="{{ old('teamAShots', $eventSchedule->teams[0]->pivot->teamShots) }}"
                                           placeholder="Input team Shot">
                                    <span class="invalid-feedback teamAShots_error" role="alert">
                                                <strong></strong>
                                            </span>
                                </div>
                            @endif
                        </div>
                        <div class="col-6 col-md-3">
                            @if($eventSchedule->isOpponentTeamMatch == 1)
                                <div class="form-group">
                                    <label class="form-label" for="teamAShots">Shots</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamAShots"
                                           name="teamAShots"
                                           min="0"
                                           required
                                           value="{{ old('teamAShots', $eventSchedule->teams[0]->pivot->teamShots) }}"
                                           placeholder="Input team Shot">
                                    <span class="invalid-feedback teamAShots_error" role="alert">
                                                <strong></strong>
                                            </span>
                                </div>
                            @endif
                            <div class="form-group">
                                <label class="form-label" for="teamATouches">Touches</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="teamATouches"
                                       name="teamATouches"
                                       min="0"
                                        required
                                       value="{{ old('teamATouches', $eventSchedule->teams[0]->pivot->teamTouches) }}"
                                       placeholder="Input team Touches">
                                <span class="invalid-feedback teamATouches_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="teamAPasses">Passes</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="teamAPasses"
                                       name="teamAPasses"
                                       min="0"

                                       value="{{ old('teamAPasses', $eventSchedule->teams[0]->pivot->teamPasses) }}"
                                       placeholder="Input team Passes">
                                <span class="invalid-feedback teamAPasses_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="teamATackles">Tackles</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="teamATackles"
                                       name="teamATackles"
                                       min="0"

                                       value="{{ old('teamATackles', $eventSchedule->teams[0]->pivot->teamTackles) }}"
                                       placeholder="Input team Tackles">
                                <span class="invalid-feedback teamATackles_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="form-group">
                                <label class="form-label" for="teamAClearances">Clearances</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="teamAClearances"
                                       name="teamAClearances"
                                       min="0"

                                       value="{{ old('teamAClearances', $eventSchedule->teams[0]->pivot->teamClearances) }}"
                                       placeholder="Input team Clearances">
                                <span class="invalid-feedback teamAClearances_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="teamACorners">Corners</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="teamACorners"
                                       name="teamACorners"
                                       min="0"

                                       value="{{ old('teamACorners', $eventSchedule->teams[0]->pivot->teamCorners) }}"
                                       placeholder="Input team Corners">
                                <span class="invalid-feedback teamACorners_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="teamAOffsides">Offsides</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="teamAOffsides"
                                       name="teamAOffsides"
                                       min="0"

                                       value="{{ old('teamAOffsides', $eventSchedule->teams[0]->pivot->teamOffsides) }}"
                                       placeholder="Input team Offsides">
                                <span class="invalid-feedback teamAOffsides_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="form-group">
                                <label class="form-label" for="teamAYellowCards">Yellow Cards</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="teamAYellowCards"
                                       name="teamAYellowCards"
                                       min="0"

                                       value="{{ old('teamAYellowCards', $eventSchedule->teams[0]->pivot->teamYellowCards) }}"
                                       placeholder="Input team Yellow Cards">
                                <span class="invalid-feedback teamAYellowCards_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="teamARedCards">Red Cards</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="teamARedCards"
                                       name="teamARedCards"
                                       min="0"

                                       value="{{ old('teamARedCards', $eventSchedule->teams[0]->pivot->teamRedCards) }}"
                                       placeholder="Input team Red Cards">
                                <span class="invalid-feedback teamARedCards_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="teamAFoulsConceded">Fouls Conceded</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="teamAFoulsConceded"
                                       name="teamAFoulsConceded"
                                       min="0"

                                       value="{{ old('teamAFoulsConceded', $eventSchedule->teams[0]->pivot->teamFoulsConceded) }}"
                                       placeholder="Input team Fouls Conceded">
                                <span class="invalid-feedback teamAFoulsConceded_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                        </div>
                    </div>
                    <div class="page-separator">
                        <img src="{{ Storage::url($eventSchedule->teams[1]->logo) }}"
                             width="50"
                             height="50"
                             class="rounded-circle img-object-fit-cover mr-3"
                             alt="team-logo">
                        <div class="page-separator__text">{{ $eventSchedule->teams[1]->teamName }} Match
                            Stats
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-3">
                            @if ($eventSchedule->matchType != 'Internal Match')
                                <div class="form-group">
                                    <label class="form-label" for="teamBTeamScore">Team Score</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                        class="form-control"
                                        id="teamBTeamScore"
                                        name="teamBTeamScore"
                                        min="0"
                                        max="100"
                                        value="{{ old('teamBTeamScore', $eventSchedule->teams[1]->pivot->teamScore) }}"
                                        placeholder="Input team Team Score">
                                    <span class="invalid-feedback teamBTeamScore_error" role="alert">
                                            <strong></strong>
                                        </span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="teamBOwnGoal">Own Goal</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                        class="form-control"
                                        id="teamBOwnGoal"
                                        name="teamBOwnGoal"
                                        min="0"
                                        value="{{ old('teamBOwnGoal', $eventSchedule->teams[1]->pivot->teamOwnGoal) }}"
                                        placeholder="Input team own goal">
                                    <span class="invalid-feedback teamBOwnGoal_error" role="alert">
                                            <strong></strong>
                                        </span>
                                </div>
                            @endif
                            <div class="form-group">
                                <label class="form-label" for="teamBPossession">Possession</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="teamBPossession"
                                       name="teamBPossession"
                                       min="0"
                                       max="100"
                                       value="{{ old('teamBPossession', $eventSchedule->teams[1]->pivot->teamPossesion) }}"
                                       placeholder="Input team possession">
                                <span class="invalid-feedback teamBPossession_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="teamBShotOnTarget">Shot on Target</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="teamBShotOnTarget"
                                       name="teamBShotOnTarget"
                                       min="0"
                                       value="{{ old('teamBShotOnTarget', $eventSchedule->teams[1]->pivot->teamShotOnTarget) }}"
                                       placeholder="Input team Shot on Target">
                                <span class="invalid-feedback teamBShotOnTarget_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="form-group">
                                <label class="form-label" for="teamBShots">Shots</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="teamBShots"
                                       name="teamBShots"
                                       min="0"

                                       value="{{ old('teamBShots', $eventSchedule->teams[1]->pivot->teamShots) }}"
                                       placeholder="Input team Shot">
                                <span class="invalid-feedback teamBShots_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="teamBTouches">Touches</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="teamBTouches"
                                       name="teamBTouches"
                                       min="0"

                                       value="{{ old('teamBTouches', $eventSchedule->teams[1]->pivot->teamTouches) }}"
                                       placeholder="Input team Touches">
                                <span class="invalid-feedback teamBTouches_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="teamBPasses">Passes</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="teamBPasses"
                                       name="teamBPasses"
                                       min="0"
                                       value="{{ old('teamBPasses', $eventSchedule->teams[1]->pivot->teamPasses) }}"
                                       placeholder="Input team Passes">
                                <span class="invalid-feedback teamBPasses_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="teamBTackles">Tackles</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="teamBTackles"
                                       name="teamBTackles"
                                       min="0"

                                       value="{{ old('teamBTackles', $eventSchedule->teams[1]->pivot->teamTackles) }}"
                                       placeholder="Input team Tackles">
                                <span class="invalid-feedback teamBTackles_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="form-group">
                                <label class="form-label" for="teamBClearances">Clearances</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="teamBClearances"
                                       name="teamBClearances"
                                       min="0"

                                       value="{{ old('teamBClearances', $eventSchedule->teams[1]->pivot->teamClearances) }}"
                                       placeholder="Input team Clearances">
                                <span class="invalid-feedback teamBClearances_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="teamBCorners">Corners</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="teamBCorners"
                                       name="teamBCorners"
                                       min="0"

                                       value="{{ old('teamBCorners', $eventSchedule->teams[1]->pivot->teamCorners) }}"
                                       placeholder="Input team Corners">
                                <span class="invalid-feedback teamBCorners_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="teamBOffsides">Offsides</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="teamBOffsides"
                                       name="teamBOffsides"
                                       min="0"

                                       value="{{ old('teamBOffsides', $eventSchedule->teams[1]->pivot->teamOffsides) }}"
                                       placeholder="Input team Offsides">
                                <span class="invalid-feedback teamBOffsides_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="form-group">
                                <label class="form-label" for="teamBYellowCards">Yellow Cards</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="teamBYellowCards"
                                       name="teamBYellowCards"
                                       min="0"

                                       value="{{ old('teamBYellowCards', $eventSchedule->teams[1]->pivot->teamYellowCards) }}"
                                       placeholder="Input team Yellow Cards">
                                <span class="invalid-feedback teamBYellowCards_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="teamBRedCards">Red Cards</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="teamBRedCards"
                                       name="teamBRedCards"
                                       min="0"

                                       value="{{ old('teamBRedCards', $eventSchedule->teams[1]->pivot->teamRedCards) }}"
                                       placeholder="Input team Red Cards">
                                <span class="invalid-feedback teamBRedCards_error" role="alert">
                                        <strong></strong>
                                    </span>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="teamBFoulsConceded">Fouls Conceded</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       class="form-control"
                                       id="teamBFoulsConceded"
                                       name="teamBFoulsConceded"
                                       min="0"
                                       value="{{ old('teamBFoulsConceded', $eventSchedule->teams[1]->pivot->teamFoulsConceded) }}"
                                       placeholder="Input team Fouls Conceded">
                                <span class="invalid-feedback teamBFoulsConceded_error" role="alert">
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
{{-- update team match stats data --}}
<x-modal-form-update-processing formId="#formTeamMatchStats"
                                updateDataId=""
                                :routeUpdate="route('match-schedules.update-match-stats', $eventSchedule->id)"
                                modalId="#teamMatchStatsModal"/>

@push('addon-script')
    <script>
        $(document).ready(function (){
            const formId = '#formTeamMatchStats';

            // show create team scorer modal
            $('#updateMatchStats').on('click', function (e) {
                e.preventDefault();
                $('#teamMatchStatsModal').modal('show');
                $(formId+' .invalid-feedback').empty();
                $(formId+' input').removeClass('is-invalid');
            });
        });
    </script>
@endpush
