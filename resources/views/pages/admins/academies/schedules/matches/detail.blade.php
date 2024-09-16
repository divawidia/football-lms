@extends('layouts.master')
@section('title')
{{--    @dd($data['dataSchedule']->teams)--}}
    Match {{ $data['dataSchedule']->teams[0]->teamName }} Vs {{ $data['dataSchedule']->teams[1]->teamName }}
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <!-- Modal edit player attendance -->
    <div class="modal fade" id="editPlayerAttendanceModal" tabindex="-1" aria-labelledby="editPlayerAttendanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="" method="post" id="formEditPlayerAttendanceModal">
                    @method('PUT')
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="playerName"></h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="playerId">
                        <div class="form-group">
                            <label class="form-label" for="add_attendanceStatus">Attendance Status</label>
                            <small class="text-danger">*</small>
                            <select class="form-control form-select" id="add_attendanceStatus" name="attendanceStatus" required>
                                <option disabled selected>Select player's attendance status</option>
                                @foreach(['Attended', 'Illness', 'Injured', 'Other'] AS $type)
                                    <option value="{{ $type }}" @selected(old('attendanceStatus') == $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                            <span class="invalid-feedback attendanceStatus_error" role="alert">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="add_note">Note</label>
                            <small>(Optional)</small>
                            <textarea class="form-control" id="add_note" name="note" placeholder="Input the detailed absent reason (if not attended)">{{ old('note') }}</textarea>
                            <span class="invalid-feedback note_error" role="alert">
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

    <!-- Modal edit coach attendance -->
    <div class="modal fade" id="editCoachAttendanceModal" tabindex="-1" aria-labelledby="editCoachAttendanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="" method="post" id="formEditCoachAttendanceModal">
                    @method('PUT')
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="coachName"></h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="coachId">
                        <div class="form-group">
                            <label class="form-label" for="add_attendanceStatus">Attendance Status</label>
                            <small class="text-danger">*</small>
                            <select class="form-control form-select" id="add_attendanceStatus" name="attendanceStatus" required>
                                <option disabled selected>Select Coach's attendance status</option>
                                @foreach(['Attended', 'Illness', 'Injured', 'Other'] AS $type)
                                    <option value="{{ $type }}" @selected(old('attendanceStatus') == $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                            <span class="invalid-feedback attendanceStatus_error" role="alert">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="add_note">Note</label>
                            <small>(Optional)</small>
                            <textarea class="form-control" id="add_note" name="note" placeholder="Input the detailed absent reason (if not attended)">{{ old('note') }}</textarea>
                            <span class="invalid-feedback note_error" role="alert">
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

    <!-- Modal create note -->
    <div class="modal fade" id="createNoteModal" tabindex="-1" aria-labelledby="createNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('training-schedules.create-note', $data['dataSchedule']->id) }}" method="post" id="formCreateNoteModal">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="coachName">Create note for {{ $data['dataSchedule']->eventName }} Session</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label" for="add_note">Note</label>
                            <small class="text-danger">*</small>
                            <textarea class="form-control" id="add_note" name="note" placeholder="Input note here ..." required>{{ old('note') }}</textarea>
                            <span class="invalid-feedback note_error" role="alert">
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

    <!-- Modal edit note -->
    <div class="modal fade" id="editNoteModal" tabindex="-1" aria-labelledby="editNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="" method="post" id="formUpdateNoteModal">
                    @method('PUT')
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="coachName">Update note for {{ $data['dataSchedule']->eventName }} Session</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="noteId">
                        <div class="form-group">
                            <label class="form-label" for="edit_note">Note</label>
                            <small class="text-danger">*</small>
                            <textarea class="form-control" id="edit_note" name="note" placeholder="Input note here ..." required></textarea>
                            <span class="invalid-feedback note_error" role="alert">
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

    <!-- Modal add team scorer -->
    <div class="modal fade" id="createTeamScorerModal" tabindex="-1" aria-labelledby="createTeamScorerModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('match-schedules.store-match-scorer', $data['dataSchedule']->id) }}" method="post" id="formAddScorerModal">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="coachName">Add team scorer of this match</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label" for="add_playerId">Player Name</label>
                            <small class="text-danger">*</small>
                            <select class="form-control form-select" id="add_playerId" name="playerId" data-toggle="select" required>
                                <option disabled selected>Select team player who scored goal</option>
                                @foreach($data['dataSchedule']->players as $player)
                                    <option value="{{ $player->id }}" @selected(old('playerId') == $player->id) data-avatar-src="{{ Storage::url($player->user->foto) }}">
                                        {{  $player->user->firstName }} {{  $player->user->lastName }} ~ {{ $player->position->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="invalid-feedback playerId_error" role="alert">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="add_assistPlayerId">Assist Player Name</label>
                            <small class="text-danger">*</small>
                            <select class="form-control form-select" id="add_assistPlayerId" name="assistPlayerId" data-toggle="select" required>
                            </select>
                            <span class="invalid-feedback assistPlayerId_error" role="alert">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="add_minuteScored">Minute Scored</label>
                            <small class="text-danger">*</small>
                            <input type="number"
                                   class="form-control"
                                   id="add_minuteScored"
                                   name="minuteScored"
                                   min="1"
                                   max="160"
                                   value="{{ old('minuteScored') }}"
                                   placeholder="Pick minutes the player scored the goal. Eg : 60">
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

    <!-- Modal add own goal -->
    <div class="modal fade" id="createTeamOwnGoalModal" tabindex="-1" aria-labelledby="createTeamOwnGoalModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('match-schedules.store-own-goal', $data['dataSchedule']->id) }}" method="post" id="formAddOwnGoalModal">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="coachName">Add own goal of this match</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label" for="add_playerId">Player Name</label>
                            <small class="text-danger">*</small>
                            <select class="form-control form-select" id="add_playerId" name="playerId" data-toggle="select" required>
                                <option disabled selected>Select team player who scored goal</option>
                                @foreach($data['dataSchedule']->players as $player)
                                    <option value="{{ $player->id }}" @selected(old('playerId') == $player->id) data-avatar-src="{{ Storage::url($player->user->foto) }}">
                                        {{  $player->user->firstName }} {{  $player->user->lastName }} ~ {{ $player->position->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="invalid-feedback playerId_error" role="alert">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="add_minuteScored">Minute Scored</label>
                            <small class="text-danger">*</small>
                            <input type="number"
                                   class="form-control"
                                   id="add_minuteScored"
                                   name="minuteScored"
                                   min="1"
                                   max="160"
                                   value="{{ old('minuteScored') }}"
                                   placeholder="Pick minutes the player scored the own goal. Eg : 60">
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

    <!-- Modal add team match stats -->
    <div class="modal fade" id="teamMatchStatsModal" tabindex="-1" aria-labelledby="teamMatchStatsModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('match-schedules.update-match-stats', $data['dataSchedule']->id) }}" method="post" id="formTeamMatchStats">
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
                            <img src="{{ Storage::url($data['dataSchedule']->teams[0]->logo) }}"
                                 width="50"
                                 height="50"
                                 class="rounded-circle img-object-fit-cover mr-3"
                                 alt="team-logo">
                            <div class="page-separator__text">{{ $data['dataSchedule']->teams[0]->teamName }} Match Stats</div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label" for="teamAOwnGoal">Own Goal</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamAOwnGoal"
                                           name="teamAOwnGoal"
                                           min="0"
                                           value="{{ old('teamAOwnGoal', $data['dataSchedule']->teams[0]->pivot->teamOwnGoal) }}"
                                           placeholder="Input team own goal">
                                    <span class="invalid-feedback teamAOwnGoal_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="teamAPossession">Possession</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamAPossession"
                                           name="teamAPossession"
                                           min="0"
                                           max="100"
                                           value="{{ old('teamAPossession', $data['dataSchedule']->teams[0]->pivot->teamPossesion) }}"
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
                                           value="{{ old('teamAShotOnTarget', $data['dataSchedule']->teams[0]->pivot->teamShotOnTarget) }}"
                                           placeholder="Input team Shot on Target">
                                    <span class="invalid-feedback teamAShotOnTarget_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="teamAShots">Shots</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamAShots"
                                           name="teamAShots"
                                           min="0"

                                           value="{{ old('teamAShots', $data['dataSchedule']->teams[0]->pivot->teamShots) }}"
                                           placeholder="Input team Shot">
                                    <span class="invalid-feedback teamAShots_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="teamATouches">Touches</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamATouches"
                                           name="teamATouches"
                                           min="0"

                                           value="{{ old('teamATouches', $data['dataSchedule']->teams[0]->pivot->teamTouches) }}"
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

                                           value="{{ old('teamAPasses', $data['dataSchedule']->teams[0]->pivot->teamPasses) }}"
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

                                           value="{{ old('teamATackles', $data['dataSchedule']->teams[0]->pivot->teamTackles) }}"
                                           placeholder="Input team Tackles">
                                    <span class="invalid-feedback teamATackles_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label" for="teamAClearances">Clearances</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamAClearances"
                                           name="teamAClearances"
                                           min="0"

                                           value="{{ old('teamAClearances', $data['dataSchedule']->teams[0]->pivot->teamClearances) }}"
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

                                           value="{{ old('teamACorners', $data['dataSchedule']->teams[0]->pivot->teamCorners) }}"
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

                                           value="{{ old('teamAOffsides', $data['dataSchedule']->teams[0]->pivot->teamOffsides) }}"
                                           placeholder="Input team Offsides">
                                    <span class="invalid-feedback teamAOffsides_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="teamAYellowCards">Yellow Cards</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamAYellowCards"
                                           name="teamAYellowCards"
                                           min="0"

                                           value="{{ old('teamAYellowCards', $data['dataSchedule']->teams[0]->pivot->teamYellowCards) }}"
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

                                           value="{{ old('teamARedCards', $data['dataSchedule']->teams[0]->pivot->teamRedCards) }}"
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

                                           value="{{ old('teamAFoulsConceded', $data['dataSchedule']->teams[0]->pivot->teamFoulsConceded) }}"
                                           placeholder="Input team Fouls Conceded">
                                    <span class="invalid-feedback teamAFoulsConceded_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="page-separator">
                            <img src="{{ Storage::url($data['dataSchedule']->teams[1]->logo) }}"
                                 width="50"
                                 height="50"
                                 class="rounded-circle img-object-fit-cover mr-3"
                                 alt="team-logo">
                            <div class="page-separator__text">{{ $data['dataSchedule']->teams[1]->teamName }} Match Stats</div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label" for="teamBTeamScore">Team Score</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamBTeamScore"
                                           name="teamBTeamScore"
                                           min="0"
                                           max="100"
                                           value="{{ old('teamBTeamScore', $data['dataSchedule']->teams[1]->pivot->teamScore) }}"
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
                                           value="{{ old('teamBOwnGoal', $data['dataSchedule']->teams[0]->pivot->teamOwnGoal) }}"
                                           placeholder="Input team own goal">
                                    <span class="invalid-feedback teamBOwnGoal_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="teamBPossession">Possession</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamBPossession"
                                           name="teamBPossession"
                                           min="0"
                                           max="100"
                                           value="{{ old('teamBPossession', $data['dataSchedule']->teams[1]->pivot->teamPossesion) }}"
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
                                           value="{{ old('teamBShotOnTarget', $data['dataSchedule']->teams[1]->pivot->teamShotOnTarget) }}"
                                           placeholder="Input team Shot on Target">
                                    <span class="invalid-feedback teamBShotOnTarget_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="teamBShots">Shots</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamBShots"
                                           name="teamBShots"
                                           min="0"

                                           value="{{ old('teamBShots', $data['dataSchedule']->teams[1]->pivot->teamShots) }}"
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

                                           value="{{ old('teamBTouches', $data['dataSchedule']->teams[1]->pivot->teamTouches) }}"
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

                                           value="{{ old('teamBPasses', $data['dataSchedule']->teams[1]->pivot->teamPasses) }}"
                                           placeholder="Input team Passes">
                                    <span class="invalid-feedback teamBPasses_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label" for="teamBTackles">Tackles</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamBTackles"
                                           name="teamBTackles"
                                           min="0"

                                           value="{{ old('teamBTackles', $data['dataSchedule']->teams[1]->pivot->teamTackles) }}"
                                           placeholder="Input team Tackles">
                                    <span class="invalid-feedback teamBTackles_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="teamBClearances">Clearances</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamBClearances"
                                           name="teamBClearances"
                                           min="0"

                                           value="{{ old('teamBClearances', $data['dataSchedule']->teams[1]->pivot->teamClearances) }}"
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

                                           value="{{ old('teamBCorners', $data['dataSchedule']->teams[1]->pivot->teamCorners) }}"
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

                                           value="{{ old('teamBOffsides', $data['dataSchedule']->teams[1]->pivot->teamOffsides) }}"
                                           placeholder="Input team Offsides">
                                    <span class="invalid-feedback teamBOffsides_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="teamBYellowCards">Yellow Cards</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamBYellowCards"
                                           name="teamBYellowCards"
                                           min="0"

                                           value="{{ old('teamBYellowCards', $data['dataSchedule']->teams[1]->pivot->teamYellowCards) }}"
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

                                           value="{{ old('teamBRedCards', $data['dataSchedule']->teams[1]->pivot->teamRedCards) }}"
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

                                           value="{{ old('teamBFoulsConceded', $data['dataSchedule']->teams[1]->pivot->teamFoulsConceded) }}"
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

    <!-- Modal update player match stats -->
    <div class="modal fade" id="playerMatchStatsModal" tabindex="-1" aria-labelledby="playerMatchStatsModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('match-schedules.update-match-stats', $data['dataSchedule']->id) }}" method="post" id="formTeamMatchStats">
                    @method('PUT')
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="playerStatsName"></h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="page-separator">
                            <img src="{{ Storage::url($data['dataSchedule']->teams[0]->logo) }}"
                                 width="50"
                                 height="50"
                                 class="rounded-circle img-object-fit-cover mr-3"
                                 alt="team-logo">
                            <div class="page-separator__text">{{ $data['dataSchedule']->teams[0]->teamName }} Match Stats</div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label" for="teamAOwnGoal">Minutes Played</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamAOwnGoal"
                                           name="teamAOwnGoal"
                                           min="0"
                                           value="{{ old('teamAOwnGoal', $data['dataSchedule']->teams[0]->pivot->teamOwnGoal) }}"
                                           placeholder="Input team own goal">
                                    <span class="invalid-feedback teamAOwnGoal_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="teamAPossession">Goals</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamAPossession"
                                           name="teamAPossession"
                                           min="0"
                                           max="100"
                                           value="{{ old('teamAPossession', $data['dataSchedule']->teams[0]->pivot->teamPossesion) }}"
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
                                           value="{{ old('teamAShotOnTarget', $data['dataSchedule']->teams[0]->pivot->teamShotOnTarget) }}"
                                           placeholder="Input team Shot on Target">
                                    <span class="invalid-feedback teamAShotOnTarget_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="teamAShots">Shots</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamAShots"
                                           name="teamAShots"
                                           min="0"

                                           value="{{ old('teamAShots', $data['dataSchedule']->teams[0]->pivot->teamShots) }}"
                                           placeholder="Input team Shot">
                                    <span class="invalid-feedback teamAShots_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="teamATouches">Touches</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamATouches"
                                           name="teamATouches"
                                           min="0"

                                           value="{{ old('teamATouches', $data['dataSchedule']->teams[0]->pivot->teamTouches) }}"
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

                                           value="{{ old('teamAPasses', $data['dataSchedule']->teams[0]->pivot->teamPasses) }}"
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

                                           value="{{ old('teamATackles', $data['dataSchedule']->teams[0]->pivot->teamTackles) }}"
                                           placeholder="Input team Tackles">
                                    <span class="invalid-feedback teamATackles_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label" for="teamAClearances">Clearances</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamAClearances"
                                           name="teamAClearances"
                                           min="0"

                                           value="{{ old('teamAClearances', $data['dataSchedule']->teams[0]->pivot->teamClearances) }}"
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

                                           value="{{ old('teamACorners', $data['dataSchedule']->teams[0]->pivot->teamCorners) }}"
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

                                           value="{{ old('teamAOffsides', $data['dataSchedule']->teams[0]->pivot->teamOffsides) }}"
                                           placeholder="Input team Offsides">
                                    <span class="invalid-feedback teamAOffsides_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="teamAYellowCards">Yellow Cards</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamAYellowCards"
                                           name="teamAYellowCards"
                                           min="0"

                                           value="{{ old('teamAYellowCards', $data['dataSchedule']->teams[0]->pivot->teamYellowCards) }}"
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

                                           value="{{ old('teamARedCards', $data['dataSchedule']->teams[0]->pivot->teamRedCards) }}"
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

                                           value="{{ old('teamAFoulsConceded', $data['dataSchedule']->teams[0]->pivot->teamFoulsConceded) }}"
                                           placeholder="Input team Fouls Conceded">
                                    <span class="invalid-feedback teamAFoulsConceded_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="page-separator">
                            <img src="{{ Storage::url($data['dataSchedule']->teams[1]->logo) }}"
                                 width="50"
                                 height="50"
                                 class="rounded-circle img-object-fit-cover mr-3"
                                 alt="team-logo">
                            <div class="page-separator__text">{{ $data['dataSchedule']->teams[1]->teamName }} Match Stats</div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label" for="teamBTeamScore">Team Score</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamBTeamScore"
                                           name="teamBTeamScore"
                                           min="0"
                                           max="100"
                                           value="{{ old('teamBTeamScore', $data['dataSchedule']->teams[1]->pivot->teamScore) }}"
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
                                           value="{{ old('teamBOwnGoal', $data['dataSchedule']->teams[0]->pivot->teamOwnGoal) }}"
                                           placeholder="Input team own goal">
                                    <span class="invalid-feedback teamBOwnGoal_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="teamBPossession">Possession</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamBPossession"
                                           name="teamBPossession"
                                           min="0"
                                           max="100"
                                           value="{{ old('teamBPossession', $data['dataSchedule']->teams[1]->pivot->teamPossesion) }}"
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
                                           value="{{ old('teamBShotOnTarget', $data['dataSchedule']->teams[1]->pivot->teamShotOnTarget) }}"
                                           placeholder="Input team Shot on Target">
                                    <span class="invalid-feedback teamBShotOnTarget_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="teamBShots">Shots</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamBShots"
                                           name="teamBShots"
                                           min="0"

                                           value="{{ old('teamBShots', $data['dataSchedule']->teams[1]->pivot->teamShots) }}"
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

                                           value="{{ old('teamBTouches', $data['dataSchedule']->teams[1]->pivot->teamTouches) }}"
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

                                           value="{{ old('teamBPasses', $data['dataSchedule']->teams[1]->pivot->teamPasses) }}"
                                           placeholder="Input team Passes">
                                    <span class="invalid-feedback teamBPasses_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label" for="teamBTackles">Tackles</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamBTackles"
                                           name="teamBTackles"
                                           min="0"

                                           value="{{ old('teamBTackles', $data['dataSchedule']->teams[1]->pivot->teamTackles) }}"
                                           placeholder="Input team Tackles">
                                    <span class="invalid-feedback teamBTackles_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="teamBClearances">Clearances</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamBClearances"
                                           name="teamBClearances"
                                           min="0"

                                           value="{{ old('teamBClearances', $data['dataSchedule']->teams[1]->pivot->teamClearances) }}"
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

                                           value="{{ old('teamBCorners', $data['dataSchedule']->teams[1]->pivot->teamCorners) }}"
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

                                           value="{{ old('teamBOffsides', $data['dataSchedule']->teams[1]->pivot->teamOffsides) }}"
                                           placeholder="Input team Offsides">
                                    <span class="invalid-feedback teamBOffsides_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="teamBYellowCards">Yellow Cards</label>
                                    <small class="text-danger">*</small>
                                    <input type="number"
                                           class="form-control"
                                           id="teamBYellowCards"
                                           name="teamBYellowCards"
                                           min="0"

                                           value="{{ old('teamBYellowCards', $data['dataSchedule']->teams[1]->pivot->teamYellowCards) }}"
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

                                           value="{{ old('teamBRedCards', $data['dataSchedule']->teams[1]->pivot->teamRedCards) }}"
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

                                           value="{{ old('teamBFoulsConceded', $data['dataSchedule']->teams[1]->pivot->teamFoulsConceded) }}"
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
@endsection

@section('content')
    <div class="page-section bg-primary">
        <div class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <div class="flex">
                <h2 class="text-white mb-0">Match {{ $data['dataSchedule']->teams[0]->teamName }} Vs {{ $data['dataSchedule']->teams[1]->teamName }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">{{ $data['dataSchedule']->eventType }} ~ {{ $data['dataSchedule']->matchType }}
                    @if($data['dataSchedule']->competition)
                        ~ {{$data['dataSchedule']->competition->name}}
                    @endif
                </p>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-white" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Action
                    <span class="material-icons ml-3">
                        keyboard_arrow_down
                    </span>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="{{ route('training-schedules.edit', $data['dataSchedule']->id) }}"><span class="material-icons">edit</span> Edit Match Schedule</a>
                    @if($data['dataSchedule']->status == '1')
                        <form action="{{ route('deactivate-training', $data['dataSchedule']->id) }}" method="POST">
                            @method("PATCH")
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <span class="material-icons">block</span> End Match
                            </button>
                        </form>
                    @else
                        <form action="{{ route('activate-training', $data['dataSchedule']->id) }}" method="POST">
                            @method("PATCH")
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <span class="material-icons">check_circle</span> Start Match
                            </button>
                        </form>
                    @endif
                    <button type="button" class="dropdown-item delete" id="{{$data['dataSchedule']->id}}">
                        <span class="material-icons">delete</span> Delete Match
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="navbar navbar-expand-sm navbar-light bg-white border-bottom navbar-list p-0 m-0 align-items-center">
        <div class="container page__container">
            <ul class="nav navbar-nav flex align-items-sm-center">
                <li class="nav-item navbar-list__item">
                    @if($data['dataSchedule']->status == '1')
                        Status : <span class="badge badge-pill badge-success ml-1">Active</span>
                    @else
                        Status : <span class="badge badge-pill badge-danger ml-1">Ended</span>
                    @endif
                </li>
                <li class="nav-item navbar-list__item">
                    <i class="material-icons text-danger icon--left icon-16pt">event</i>
                    {{ date('D, M d Y', strtotime($data['dataSchedule']->date)) }}
                </li>
                <li class="nav-item navbar-list__item">
                    <i class="material-icons text-danger icon--left icon-16pt">schedule</i>
                    {{ date('h:i A', strtotime($data['dataSchedule']->startTime)) }} - {{ date('h:i A', strtotime($data['dataSchedule']->endTime)) }}
                </li>
                <li class="nav-item navbar-list__item">
                    <i class="material-icons text-danger icon--left icon-16pt">location_on</i>
                    {{ $data['dataSchedule']->place }}
                </li>
                <li class="nav-item navbar-list__item">
                    <div class="media align-items-center">
                        <span class="media-left mr-16pt">
                            <img src="{{Storage::url($data['dataSchedule']->user->foto) }}"
                                 width="30"
                                 alt="avatar"
                                 class="rounded-circle">
                        </span>
                        <div class="media-body">
                            <a class="card-title m-0"
                               href="">Created by {{$data['dataSchedule']->user->firstName}} {{$data['dataSchedule']->user->lastName}}</a>
                            <p class="text-50 lh-1 mb-0">Admin</p>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <div class="container page__container page-section">
        {{--    Team Match Score    --}}
        <div class="card px-lg-5">
            <div class="card-body">
                <div class="row row d-flex align-items-center">
                    <div class="col-4 d-flex">
                        <img src="{{ Storage::url($data['dataSchedule']->teams[0]->logo) }}"
                             width="50"
                             height="50"
                             class="rounded-circle img-object-fit-cover"
                             alt="instructor">
                        <div class="flex ml-3">
                            <h5 class="mb-0">{{ $data['dataSchedule']->teams[0]->teamName }}</h5>
                            <p class="text-50 lh-1 mb-0">{{ $data['dataSchedule']->teams[0]->ageGroup }}</p>
                        </div>
                    </div>
                    <div class="col-4 text-center">
                        <h2 class="mb-0">{{ $data['dataSchedule']->teams[0]->pivot->teamScore }} - {{ $data['dataSchedule']->teams[1]->pivot->teamScore }}</h2>
                    </div>
                    <div class="col-4 d-flex justify-content-end">
                        <div class="mr-3">
                            <h5 class="mb-0">{{ $data['dataSchedule']->teams[1]->teamName }}</h5>
                            <p class="text-50 lh-1 mb-0 text-right">{{ $data['dataSchedule']->teams[1]->ageGroup }}</p>
                        </div>
                        <img src="{{ Storage::url($data['dataSchedule']->teams[1]->logo) }}"
                             width="50"
                             height="50"
                             class="rounded-circle img-object-fit-cover"
                             alt="instructor">
                    </div>
                </div>
            </div>
        </div>

        {{--    Match Scorer    --}}
        <div class="page-separator">
            <div class="page-separator__text">Scorer(s)</div>
            <a href="" id="addTeamScorer" class="btn btn-primary btn-sm ml-auto"><span class="material-icons mr-2">add</span> Add team scorer</a>
        </div>
        @if(count($data['dataSchedule']->matchScores)==0)
            <div class="alert alert-light border-left-accent" role="alert">
                <div class="d-flex flex-wrap align-items-center">
                    <i class="material-icons mr-8pt">error_outline</i>
                    <div class="media-body"
                         style="min-width: 180px">
                        <small class="text-black-100">You haven't added any team scorer yet</small>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                @foreach($data['dataSchedule']->matchScores as $matchScore)
                    <div class="col-md-6">
                        <div class="card" id="{{$matchScore->id}}">
                            <div class="card-body d-flex align-items-center flex-row text-left">
                                <img src="{{ Storage::url($matchScore->player->user->foto) }}"
                                     width="50"
                                     height="50"
                                     class="rounded-circle img-object-fit-cover"
                                     alt="instructor">
                                <div class="flex ml-3">
                                    <h5 class="mb-0 d-flex">{{ $matchScore->player->user->firstName  }} {{ $matchScore->player->user->lastName  }} <p class="text-50 ml-2 mb-0">({{ $matchScore->minuteScored }}')</p></h5>
                                    <p class="text-50 lh-1 mb-0">{{ $matchScore->player->position->name }}</p>
                                    <p class="text-50 lh-1 mb-0">Assist : {{ $matchScore->assistPlayer->user->firstName }} {{ $matchScore->assistPlayer->user->lastName }}</p>
                                </div>
                                <button class="btn btn-sm btn-outline-secondary delete-scorer" type="button" id="{{ $matchScore->id }}" data-toggle="tooltip" data-placement="bottom" title="Delete scorer">
                                    <span class="material-icons">
                                        close
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{--    Match Stats    --}}
        <div class="page-separator">
            <div class="page-separator__text">Match Stats</div>
            <a href="" id="updateMatchStats" class="btn btn-primary btn-sm ml-auto"><span class="material-icons mr-2">add</span> Update match stats</a>
        </div>
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-6 d-flex align-items-center">
                        <img src="{{ Storage::url($data['dataSchedule']->teams[0]->logo) }}"
                             width="50"
                             height="50"
                             class="rounded-circle img-object-fit-cover"
                             alt="instructor">
                        <div class="flex ml-3">
                            <h5 class="mb-0">{{ $data['dataSchedule']->teams[0]->teamName }}</h5>
                        </div>
                    </div>
                    <div class="col-6 d-flex justify-content-end align-items-center">
                        <div class="mr-3">
                            <h5 class="mb-0">{{ $data['dataSchedule']->teams[1]->teamName }}</h5>
                        </div>
                        <img src="{{ Storage::url($data['dataSchedule']->teams[1]->logo) }}"
                             width="50"
                             height="50"
                             class="rounded-circle img-object-fit-cover"
                             alt="instructor">
                    </div>
                </div>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <div class="row text-center">
                        <div class="col-4">
                            <strong class="flex">{{ $data['dataSchedule']->teams[0]->pivot->teamPossesion }}</strong>
                        </div>
                        <div class="col-4">
                            <strong class="flex">Possession %</strong>
                        </div>
                        <div class="col-4">
                            <strong class="flex">{{ $data['dataSchedule']->teams[1]->pivot->teamPossesion }}</strong>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row text-center">
                        <div class="col-4">
                            <strong class="flex">{{ $data['dataSchedule']->teams[0]->pivot->teamShotOnTarget }}</strong>
                        </div>
                        <div class="col-4">
                            <strong class="flex">Shots on target</strong>
                        </div>
                        <div class="col-4">
                            <strong class="flex">{{ $data['dataSchedule']->teams[1]->pivot->teamShotOnTarget }}</strong>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row text-center">
                        <div class="col-4">
                            <strong class="flex">{{ $data['dataSchedule']->teams[0]->pivot->teamShots }}</strong>
                        </div>
                        <div class="col-4">
                            <strong class="flex">Shots</strong>
                        </div>
                        <div class="col-4">
                            <strong class="flex">{{ $data['dataSchedule']->teams[1]->pivot->teamShots }}</strong>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row text-center">
                        <div class="col-4">
                            <strong class="flex">{{ $data['dataSchedule']->teams[0]->pivot->teamTouches }}</strong>
                        </div>
                        <div class="col-4">
                            <strong class="flex">Touches</strong>
                        </div>
                        <div class="col-4">
                            <strong class="flex">{{ $data['dataSchedule']->teams[1]->pivot->teamTouches }}</strong>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row text-center">
                        <div class="col-4">
                            <strong class="flex">{{ $data['dataSchedule']->teams[0]->pivot->teamPasses }}</strong>
                        </div>
                        <div class="col-4">
                            <strong class="flex">Passes</strong>
                        </div>
                        <div class="col-4">
                            <strong class="flex">{{ $data['dataSchedule']->teams[1]->pivot->teamPasses }}</strong>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row text-center">
                        <div class="col-4">
                            <strong class="flex">{{ $data['dataSchedule']->teams[0]->pivot->teamTackles }}</strong>
                        </div>
                        <div class="col-4">
                            <strong class="flex">Tackles</strong>
                        </div>
                        <div class="col-4">
                            <strong class="flex">{{ $data['dataSchedule']->teams[1]->pivot->teamTackles }}</strong>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row text-center">
                        <div class="col-4">
                            <strong class="flex">{{ $data['dataSchedule']->teams[0]->pivot->teamClearances }}</strong>
                        </div>
                        <div class="col-4">
                            <strong class="flex">Clearances</strong>
                        </div>
                        <div class="col-4">
                            <strong class="flex">{{ $data['dataSchedule']->teams[1]->pivot->teamClearances }}</strong>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row text-center">
                        <div class="col-4">
                            <strong class="flex">{{ $data['dataSchedule']->teams[0]->pivot->teamCorners }}</strong>
                        </div>
                        <div class="col-4">
                            <strong class="flex">Corners</strong>
                        </div>
                        <div class="col-4">
                            <strong class="flex">{{ $data['dataSchedule']->teams[1]->pivot->teamCorners }}</strong>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row text-center">
                        <div class="col-4">
                            <strong class="flex">{{ $data['dataSchedule']->teams[0]->pivot->teamOffsides }}</strong>
                        </div>
                        <div class="col-4">
                            <strong class="flex">Offsides</strong>
                        </div>
                        <div class="col-4">
                            <strong class="flex">{{ $data['dataSchedule']->teams[1]->pivot->teamOffsides }}</strong>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row text-center">
                        <div class="col-4">
                            <strong class="flex">{{ $data['dataSchedule']->teams[0]->pivot->teamYellowCards }}</strong>
                        </div>
                        <div class="col-4">
                            <strong class="flex">Yellow cards</strong>
                        </div>
                        <div class="col-4">
                            <strong class="flex">{{ $data['dataSchedule']->teams[1]->pivot->teamYellowCards }}</strong>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row text-center">
                        <div class="col-4">
                            <strong class="flex">{{ $data['dataSchedule']->teams[0]->pivot->teamRedCards }}</strong>
                        </div>
                        <div class="col-4">
                            <strong class="flex">Red cards</strong>
                        </div>
                        <div class="col-4">
                            <strong class="flex">{{ $data['dataSchedule']->teams[1]->pivot->teamRedCards }}</strong>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row text-center">
                        <div class="col-4">
                            <strong class="flex">{{ $data['dataSchedule']->teams[0]->pivot->teamFoulsConceded }}</strong>
                        </div>
                        <div class="col-4">
                            <strong class="flex">Fouls conceded</strong>
                        </div>
                        <div class="col-4">
                            <strong class="flex">{{ $data['dataSchedule']->teams[1]->pivot->teamFoulsConceded }}</strong>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        {{--    Player Stats    --}}
        <div class="page-separator">
            <div class="page-separator__text">Player Stats</div>
        </div>
        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="playerStatTable">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Minutes Played</th>
                            <th>Goals</th>
                            <th>Assists</th>
                            <th>Own Goals</th>
                            <th>Shots</th>
                            <th>Passes</th>
                            <th>Fouls</th>
                            <th>Yellow Cards</th>
                            <th>Red Cards</th>
                            <th>Saves</th>
                            <th>Last Updated</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{--    Attendance Overview    --}}
        <div class="page-separator">
            <div class="page-separator__text">Attendance Overview</div>
        </div>
        <div class="row card-group-row">
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $data['totalParticipant'] }}</div>
                            <div class="flex">
                                <div class="card-title">Total Participants</div>
                            </div>
                        </div>
                        <i class='bx bxs-group icon-32pt text-danger ml-8pt' ></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $data['totalAttend'] }}</div>
                            <div class="flex">
                                <div class="card-title">Attended</div>
                            </div>
                        </div>
                        <i class='bx bxs-user-check icon-32pt text-danger ml-8pt'></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $data['totalDidntAttend'] }}</div>
                            <div class="flex">
                                <div class="card-title">Didn't Attended</div>
                            </div>
                        </div>
                        <i class='bx bxs-user-x icon-32pt text-danger ml-8pt' ></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="row card-group-row">
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $data['totalIllness'] }}</div>
                            <div class="flex">
                                <div class="card-title">Illness</div>
                            </div>
                        </div>
                        <i class='bx bxs-user-x icon-32pt text-danger ml-8pt' ></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $data['totalInjured'] }}</div>
                            <div class="flex">
                                <div class="card-title">Injured</div>
                            </div>
                        </div>
                        <i class='bx bxs-user-x icon-32pt text-danger ml-8pt' ></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $data['totalOthers'] }}</div>
                            <div class="flex">
                                <div class="card-title">Others</div>
                            </div>
                        </div>
                        <i class='bx bxs-user-x icon-32pt text-danger ml-8pt' ></i>
                    </div>
                </div>
            </div>
        </div>

        {{--    Player Attendance    --}}
        <div class="page-separator">
            <div class="page-separator__text">Player Attendance</div>
        </div>
        <div class="row">
            @foreach($data['dataSchedule']->players as $player)
                <div class="col-md-6">
                    <div class="card @if($player->pivot->attendanceStatus == 'Required Action') border-warning @elseif($player->pivot->attendanceStatus == 'Attended') border-success @else border-danger @endif" id="{{$player->id}}">
                        <div class="card-body d-flex align-items-center flex-row text-left">
                            <img src="{{ Storage::url($player->user->foto) }}"
                                 width="50"
                                 height="50"
                                 class="rounded-circle img-object-fit-cover"
                                 alt="instructor">
                            <div class="flex ml-3">
                                <h5 class="mb-0">{{ $player->user->firstName  }} {{ $player->user->lastName  }}</h5>
                                <p class="text-50 lh-1 mb-0">{{ $player->position->name }}</p>
                            </div>
                            <a class="btn @if($player->pivot->attendanceStatus == 'Required Action') btn-outline-warning text-warning @elseif($player->pivot->attendanceStatus == 'Attended') btn-outline-success text-success @else btn-outline-danger text-danger @endif playerAttendance" id="{{$player->id}}" href="">
                                <span class="material-icons mr-2">
                                    @if($player->pivot->attendanceStatus == 'Required Action') error
                                    @elseif($player->pivot->attendanceStatus == 'Attended') check_circle
                                    @else cancel
                                    @endif
                                </span>
                                {{ $player->pivot->attendanceStatus }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{--    Coach Attendance    --}}
        <div class="page-separator">
            <div class="page-separator__text">Coach Attendance</div>
        </div>
        <div class="row">
            @foreach($data['dataSchedule']->coaches as $coach)
{{--                @dd($coach->pivot->attendanceStatus)--}}
                <div class="col-md-6">
                    <div class="card @if($coach->pivot->attendanceStatus == 'Required Action') border-warning @elseif($coach->pivot->attendanceStatus == 'Attended') border-success @else border-danger @endif" id="{{$coach->id}}">
                        <div class="card-body d-flex align-items-center flex-row text-left">
                            <img src="{{ Storage::url($coach->user->foto) }}"
                                 width="50"
                                 height="50"
                                 class="rounded-circle img-object-fit-cover"
                                 alt="instructor">
                            <div class="flex ml-3">
                                <h5 class="mb-0">{{ $coach->user->firstName }} {{ $coach->user->lastName }}</h5>
                                <p class="text-50 lh-1 mb-0">{{ $coach->specializations->name }}</p>
                            </div>
                            <a class="btn @if($coach->pivot->attendanceStatus == 'Required Action') btn-outline-warning text-warning @elseif($coach->pivot->attendanceStatus == 'Attended') btn-outline-success text-success @else btn-outline-danger text-danger @endif coachAttendance" id="{{$coach->id}}" href="{{ route('training-schedules.coach', ['schedule' => $data['dataSchedule']->id, 'coach' => $coach->id]) }}">
                                        <span class="material-icons mr-2">
                                            @if($coach->pivot->attendanceStatus == 'Required Action') error
                                            @elseif($coach->pivot->attendanceStatus == 'Attended') check_circle
                                            @else cancel
                                            @endif
                                        </span>
                                {{ $coach->pivot->attendanceStatus }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{--    Training Note    --}}
        <div class="page-separator">
            <div class="page-separator__text">Match Note</div>
            <a href="" id="addNewNote" class="btn btn-primary btn-sm ml-auto"><span class="material-icons mr-2">add</span> Add new note</a>
        </div>
        @if(count($data['dataSchedule']->notes)==0)
            <div class="alert alert-light border-left-accent" role="alert">
                <div class="d-flex flex-wrap align-items-center">
                    <i class="material-icons mr-8pt">error_outline</i>
                    <div class="media-body"
                         style="min-width: 180px">
                        <small class="text-black-100">You haven't added any note scorer yet</small>
                    </div>
                </div>
            </div>
        @else
            @foreach($data['dataSchedule']->notes as $note)
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <div class="flex">
                            <h4 class="card-title">{{ date('D, M d Y h:i A', strtotime($note->created_at)) }}</h4>
                            <div class="card-subtitle text-50">Last updated at {{ date('D, M d Y h:i A', strtotime($note->updated_at)) }}</div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item edit-note" id="{{ $note->id }}" href="">
                                    <span class="material-icons">edit</span>
                                    Edit Note
                                </a>
                                <button type="button" class="dropdown-item delete-note" id="{{ $note->id }}">
                                    <span class="material-icons">delete</span>
                                    Delete Note
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @php
                            echo $note->note
                        @endphp
                    </div>
                </div>
            @endforeach
        @endif
    </div>

@endsection
@push('addon-script')
    <script>
        $(document).ready(function() {
            const playerStatTable = $('#playerStatTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! route('match-schedules.index-player-match-stats', $data['dataSchedule']->id) !!}',
                },
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'pivot.minutesPlayed', name: 'pivot.minutesPlayed'},
                    {data: 'pivot.goals', name: 'pivot.goals'},
                    {data: 'pivot.assists', name: 'pivot.assists'},
                    {data: 'pivot.ownGoal', name: 'pivot.ownGoal'},
                    {data: 'pivot.shots', name: 'pivot.shots'},
                    {data: 'pivot.passes', name: 'pivot.passes'},
                    {data: 'pivot.fouls', name: 'pivot.fouls'},
                    {data: 'pivot.yellowCards', name: 'pivot.yellowCards'},
                    {data: 'pivot.redCards', name: 'pivot.redCards'},
                    {data: 'pivot.saves', name: 'pivot.saves'},
                    {data: 'updated_at', name: 'updated_at'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ]
            });

            $('.playerAttendance').on('click', function (e) {
                e.preventDefault();
                const id = $(this).attr('id');

                $.ajax({
                    url: "{{ route('training-schedules.player', ['schedule' => $data['dataSchedule']->id, 'player' => ":id"]) }}".replace(':id', id),
                    type: 'get',
                    success: function (res) {
                        $('#editPlayerAttendanceModal').modal('show');

                        const heading = document.getElementById('playerName');
                        heading.textContent = 'Update Player ' + res.data.user.firstName + ' ' + res.data.user.lastName + ' Attendance';
                        $('#editPlayerAttendanceModal #add_attendanceStatus').val(res.data.schedules[0].pivot.attendanceStatus);
                        $('#editPlayerAttendanceModal #add_note').val(res.data.schedules[0].pivot.note);
                        $('#playerId').val(res.data.id);
                    },
                    error: function (error) {
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when deleting data!",
                            text: error,
                        });
                    }
                });
            });

            $('.coachAttendance').on('click', function (e) {
                e.preventDefault();
                const id = $(this).attr('id');

                $.ajax({
                    url: "{{ route('training-schedules.coach', ['schedule' => $data['dataSchedule']->id, 'coach' => ":id"]) }}".replace(':id', id),
                    type: 'get',
                    success: function (res) {
                        $('#editCoachAttendanceModal').modal('show');

                        const heading = document.getElementById('coachName');
                        heading.textContent = 'Update Coach ' + res.data.user.firstName + ' ' + res.data.user.lastName + ' Attendance';
                        $('#editCoachAttendanceModal #add_attendanceStatus').val(res.data.schedules[0].pivot.attendanceStatus);
                        $('#editCoachAttendanceModal #add_note').val(res.data.schedules[0].pivot.note);
                        $('#coachId').val(res.data.id);
                    },
                    error: function (error) {
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when deleting data!",
                            text: error,
                        });
                    }
                });
            });

            $('#addNewNote').on('click', function (e) {
                e.preventDefault();
                $('#createNoteModal').modal('show');
            });

            $('.edit-note').on('click', function (e) {
                e.preventDefault();
                const id = $(this).attr('id');

                $.ajax({
                    url: "{{ route('training-schedules.edit-note', ['schedule' => $data['dataSchedule']->id, 'note' => ":id"]) }}".replace(':id', id),
                    type: 'get',
                    success: function (res) {
                        $('#editNoteModal').modal('show');

                        const heading = document.getElementById('edit_note');
                        heading.textContent = res.data.note;
                        $('#noteId').val(res.data.id);
                    },
                    error: function (error) {
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when deleting data!",
                            text: error,
                        });
                    }
                });
            });

            // update player attendance data
            $('#formEditPlayerAttendanceModal').on('submit', function (e) {
                e.preventDefault();
                const id = $('#playerId').val();
                $.ajax({
                    url: "{{ route('training-schedules.update-player', ['schedule' => $data['dataSchedule']->id, 'player' => ":id"]) }}".replace(':id', id),
                    type: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function (res) {
                        $('#editPlayerAttendanceModal').modal('hide');
                        Swal.fire({
                            title: 'Player attendance successfully updated!',
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: "#1ac2a1",
                            confirmButtonText:
                                'Ok!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    },
                    error: function (xhr) {
                        const response = JSON.parse(xhr.responseText);
                        console.log(response);
                        $.each(response.errors, function (key, val) {
                            $('span.' + key + '_error').text(val[0]);
                            $("#add_" + key).addClass('is-invalid');
                        });
                    }
                });
            });

            // update coach attendance data
            $('#formEditCoachAttendanceModal').on('submit', function (e) {
                e.preventDefault();
                const id = $('#coachId').val();
                $.ajax({
                    url: "{{ route('training-schedules.update-coach', ['schedule' => $data['dataSchedule']->id, 'coach' => ":id"]) }}".replace(':id', id),
                    type: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function (res) {
                        $('#editCoachAttendanceModal').modal('hide');
                        Swal.fire({
                            title: 'Coach attendance successfully updated!',
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: "#1ac2a1",
                            confirmButtonText:
                                'Ok!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    },
                    error: function (xhr) {
                        const response = JSON.parse(xhr.responseText);
                        console.log(response);
                        $.each(response.errors, function (key, val) {
                            $('span.' + key + '_error').text(val[0]);
                            $("#add_" + key).addClass('is-invalid');
                        });
                    }
                });
            });

            // create schedule note data
            $('#formCreateNoteModal').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    method: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function (res) {
                        $('#createNoteModal').modal('hide');
                        Swal.fire({
                            title: 'Training session note successfully added!',
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: "#1ac2a1",
                            confirmButtonText:
                                'Ok!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    },
                    error: function (xhr) {
                        const response = JSON.parse(xhr.responseText);
                        console.log(response);
                        $.each(response.errors, function (key, val) {
                            $('span.' + key + '_error').text(val[0]);
                            $("#add_" + key).addClass('is-invalid');
                        });
                    }
                });
            });

            // update schedule note data
            $('#formUpdateNoteModal').on('submit', function (e) {
                e.preventDefault();
                const id = $('#noteId').val();
                $.ajax({
                    url: "{{ route('training-schedules.update-note', ['schedule' => $data['dataSchedule']->id, 'note' => ":id"]) }}".replace(':id', id),
                    type: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function (res) {
                        $('#editNoteModal').modal('hide');
                        Swal.fire({
                            title: 'Training session note successfully updated!',
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: "#1ac2a1",
                            confirmButtonText:
                                'Ok!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    },
                    error: function (xhr) {
                        const response = JSON.parse(xhr.responseText);
                        console.log(response);
                        $.each(response.errors, function (key, val) {
                            $('span.' + key + '_error').text(val[0]);
                            $("#edit_" + key).addClass('is-invalid');
                        });
                    }
                });
            });

            // delete competition alert
            $('body').on('click', '.delete', function () {
                let id = $(this).attr('id');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('training-schedules.destroy', ['schedule' => ':id']) }}".replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function () {
                                Swal.fire({
                                    title: 'Competition successfully deleted!',
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonColor: "#1ac2a1",
                                    confirmButtonText:
                                        'Ok!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "{{ route('training-schedules.index') }}";
                                    }
                                });
                            },
                            error: function (error) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Something went wrong when deleting data!",
                                    text: error,
                                });
                            }
                        });
                    }
                });
            });

            // delete schedule note alert
            $('body').on('click', '.delete-note', function () {
                let id = $(this).attr('id');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('training-schedules.destroy-note', ['schedule' => $data['dataSchedule']->id, 'note'=>':id']) }}".replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function () {
                                Swal.fire({
                                    title: 'Competition successfully deleted!',
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonColor: "#1ac2a1",
                                    confirmButtonText:
                                        'Ok!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                });
                            },
                            error: function (error) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Something went wrong when deleting data!",
                                    text: error,
                                });
                            }
                        });
                    }
                });
            });

            // show create team scorer modal
            $('#addTeamScorer').on('click', function (e) {
                e.preventDefault();
                $('#createTeamScorerModal').modal('show');
            });

            // fetch assist player data
            $('#add_playerId').on('change', function () {
                const id = this.value;
                $.ajax({
                    url: "{{route('get-assist-player', ['schedule' => $data['dataSchedule']->id,'player'=>':id']) }}".replace(':id', id),
                    type: 'GET',
                    dataType: 'json',
                    success: function (result) {
                        $('#add_assistPlayerId').html('<option disabled selected>Select player who assisted scoring the goal</option>');
                        $.each(result.data, function (key, value) {
                            $('#add_assistPlayerId').append('<option value="' + value.id + '" data-avatar-src="' + '{{ Storage::url('') }}' + value.user.foto + '">' + value.user.firstName + ' ' + value.user.lastName + ' ~ ' + value.position.name + '</option>');
                        });
                    }
                });
            });

            // store team scorer data
            $('#formAddScorerModal').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    method: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function (res) {
                        $('#createTeamScorerModal').modal('hide');
                        Swal.fire({
                            title: 'Match scorer successfully added!',
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: "#1ac2a1",
                            confirmButtonText:
                                'Ok!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    },
                    error: function (xhr) {
                        const response = JSON.parse(xhr.responseText);
                        console.log(response);
                        $.each(response.errors, function (key, val) {
                            $('span.' + key + '_error').text(val[0]);
                            $("#edit_" + key).addClass('is-invalid');
                        });
                    }
                });
            });

            // delete team scorer with alert
            $('body').on('click', '.delete-scorer', function () {
                let id = $(this).attr('id');

                Swal.fire({
                    title: "Are you sure to delete this scorer?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('match-schedules.destroy-match-scorer', ['schedule' => $data['dataSchedule']->id, 'scorer'=>':id']) }}".replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function () {
                                Swal.fire({
                                    title: 'Match Scorer successfully deleted!',
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonColor: "#1ac2a1",
                                    confirmButtonText:
                                        'Ok!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                });
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Something went wrong when deleting data!",
                                    text: textStatus, errorThrown,
                                });
                            }
                        });
                    }
                });
            });

            // show update match stats modal
            $('#updateMatchStats').on('click', function (e) {
                e.preventDefault();
                $('#teamMatchStatsModal').modal('show');
            });

            // update team match stats data
            $('#formTeamMatchStats').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    method: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function (res) {
                        $('#teamMatchStatsModal').modal('hide');
                        Swal.fire({
                            title: 'Match stats successfully added!',
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: "#1ac2a1",
                            confirmButtonText:
                                'Ok!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    },
                    error: function (xhr) {
                        const response = JSON.parse(xhr.responseText);
                        console.log(response);
                        $.each(response.errors, function (key, val) {
                            $('span.' + key + '_error').text(val[0]);
                            $("#" + key).addClass('is-invalid');
                        });
                    }
                });
            });
        });
    </script>
@endpush
