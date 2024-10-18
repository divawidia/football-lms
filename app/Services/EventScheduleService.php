<?php

namespace App\Services;

use App\Models\Coach;
use App\Models\EventSchedule;
use App\Models\MatchScore;
use App\Models\Player;
use App\Models\PlayerMatchStats;
use App\Models\ScheduleNote;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use function PHPUnit\Framework\isFalse;

class EventScheduleService extends Service
{
    public function indexMatch(): Collection
    {
        return EventSchedule::with('teams', 'competition')
            ->where('eventType', 'Match')
            ->where('status', '1')
            ->get();
    }
    public function indexTraining(): Collection
    {
        return EventSchedule::with('teams')
            ->where('eventType', 'Training')
            ->orderBy('date')
            ->get();
    }

    public function coachTeamsIndexTraining(Coach $coach): Collection
    {
        $teams = $this->coachManagedTeams($coach);
        return EventSchedule::with('teams', 'competition')
            ->whereHas('teams', function($q) use ($teams) {
                $q->where('teamId', $teams[0]->id);

                // if teams are more than 1 then iterate more
                if (count($teams)>1){
                    for ($i = 1; $i < count($teams); $i++){
                        $q->orWhere('teamId', $teams[$i]->id);
                    }
                }
            })
            ->where('eventType', 'Training')
            ->where('status', '1')
            ->orderBy('date', 'desc')
            ->get();
    }

    public function makeMatchCalendar($matchesData)
    {
        $events = [];
        foreach ($matchesData as $match) {
            $events[] = [
                'id' => $match->id,
                'title' => $match->teams[0]->teamName .' Vs. '.$match->teams[1]->teamName,
                'start' => $match->date.' '.$match->startTime,
                'end' => $match->date.' '.$match->endTime,
                'className' => 'bg-primary text-white'
            ];
        }
        return $events;
    }

    public function makeTrainingCalendar($trainingsData)
    {
        $events = [];
        foreach ($trainingsData as $training) {
            $events[] = [
                'id' => $training->id,
                'title' => $training->teams[0]->teamName.' - '.$training->eventName,
                'start' => $training->date.' '.$training->startTime,
                'end' => $training->date.' '.$training->endTime,
                'className' => 'bg-warning'
            ];
        }
        return $events;
    }

    public function matchCalendar(){
        $matches = $this->indexMatch();

        return $this->makeMatchCalendar($matches);
    }

    public function trainingCalendar(){
        $trainings = $this->indexTraining();

        return $this->makeTrainingCalendar($trainings);
    }

    public function coachTeamsTrainingCalendar(Coach $coach){
        $trainings = $this->coachTeamsIndexTraining($coach);

        return $this->makeTrainingCalendar($trainings);
    }

    public function makeDataTablesTraining($trainingData)
    {
        return Datatables::of($trainingData)
            ->addColumn('action', function ($item) {
                if ($item->status == '1') {
                    $statusButton = '<form action="' . route('deactivate-training', $item->id) . '" method="POST">
                                                ' . method_field("PATCH") . '
                                                ' . csrf_field() . '
                                                <button type="submit" class="dropdown-item">
                                                    <span class="material-icons">block</span> End Training
                                                </button>
                                            </form>';
                } else {
                    $statusButton = '<form action="' . route('activate-training', $item->id) . '" method="POST">
                                                ' . method_field("PATCH") . '
                                                ' . csrf_field() . '
                                                <button type="submit" class="dropdown-item">
                                                    <span class="material-icons">check_circle</span> Start Training
                                                </button>
                                            </form>';
                }
                return '
                        <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="' . route('training-schedules.edit', $item->id) . '"><span class="material-icons">edit</span> Edit Schedule</a>
                            <a class="dropdown-item" href="' . route('training-schedules.show', $item->id) . '"><span class="material-icons">visibility</span> View Schedule</a>
                            ' . $statusButton . '
                            <button type="button" class="dropdown-item delete" id="' . $item->id . '">
                                <span class="material-icons">delete</span> Delete Schedule
                            </button>
                          </div>
                        </div>';
            })
            ->editColumn('team', function ($item) {
                return '
                        <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->teams[0]->logo) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->teams[0]->teamName . '</strong></p>
                                            <small class="js-lists-values-email text-50">'.$item->teams[0]->ageGroup.'</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            })
            ->editColumn('date', function ($item) {
                $date = date('M d, Y', strtotime($item->date));
                $startTime = date('h:i A', strtotime($item->startTime));
                $endTime = date('h:i A', strtotime($item->endTime));
                return $date.' ('.$startTime.' - '.$endTime.')';
            })
            ->editColumn('status', function ($item) {
                if ($item->status == '1') {
                    return '<span class="badge badge-pill badge-success">Active</span>';
                } elseif ($item->status == '0') {
                    return '<span class="badge badge-pill badge-danger">Ended</span>';
                }
            })
            ->rawColumns(['action','team','date','status'])
            ->make();
    }

    public function dataTablesTraining(){
        $data = $this->indexTraining();
        return $this->makeDataTablesTraining($data);
    }

    public function coachTeamsDataTablesTraining(Coach $coach){
        $data = $this->coachTeamsIndexTraining($coach);
        return $this->makeDataTablesTraining($data);
    }

    public function makeDataTablesMatch($matchData)
    {
        return Datatables::of($matchData)
            ->addColumn('action', function ($item) {
                if (Auth::user()->hasRole('coach')){
                    return '
                        <a class="btn btn-sm btn-outline-secondary" href="' . route('match-schedules.show', $item->id) . '" data-toggle="tooltips" data-placement="bottom" title="View Player">
                            <span class="material-icons">
                                visibility
                            </span>
                        </a>';
                } elseif (Auth::user()->hasRole('admin')){
                    if ($item->status == '1') {
                        $statusButton = '<form action="' . route('end-match', $item->id) . '" method="POST">
                                            ' . method_field("PATCH") . '
                                            ' . csrf_field() . '
                                            <button type="submit" class="dropdown-item">
                                                <span class="material-icons">block</span> End Match
                                            </button>
                                        </form>';
                    } else {
                        $statusButton = '<form action="' . route('activate-match', $item->id) . '" method="POST">
                                            ' . method_field("PATCH") . '
                                            ' . csrf_field() . '
                                            <button type="submit" class="dropdown-item">
                                                <span class="material-icons">check_circle</span> Start Match
                                            </button>
                                        </form>';
                    }
                    return '
                        <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="' . route('match-schedules.edit', $item->id) . '"><span class="material-icons">edit</span> Edit Schedule</a>
                            <a class="dropdown-item" href="' . route('match-schedules.show', $item->id) . '"><span class="material-icons">visibility</span> View Schedule</a>
                            ' . $statusButton . '
                            <button type="button" class="dropdown-item delete" id="' . $item->id . '">
                                <span class="material-icons">delete</span> Delete Schedule
                            </button>
                          </div>
                        </div>';
                }
            })
            ->editColumn('team', function ($item) {
                return '
                        <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->teams[0]->logo) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->teams[0]->teamName . '</strong></p>
                                            <small class="js-lists-values-email text-50">'.$item->teams[0]->ageGroup.'</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            })
            ->editColumn('opponentTeam', function ($item) {
                return '
                        <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->teams[1]->logo) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->teams[1]->teamName . '</strong></p>
                                            <small class="js-lists-values-email text-50">'.$item->teams[1]->ageGroup.'</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            })
            ->editColumn('competition', function ($item) {
                if ($item->competition){
                    $competition = '
                            <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->competition->logo) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->competition->name . '</strong></p>
                                            <small class="js-lists-values-email text-50">'.$item->competition->type.'</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                }else{
                    $competition = 'No Competition';
                }
                return $competition;
            })
            ->editColumn('date', function ($item) {
                $date = date('M d, Y', strtotime($item->date));
                $startTime = date('h:i A', strtotime($item->startTime));
                $endTime = date('h:i A', strtotime($item->endTime));
                return $date.' ('.$startTime.' - '.$endTime.')';
            })
            ->editColumn('status', function ($item) {
                if ($item->status == '1') {
                    return '<span class="badge badge-pill badge-success">Active</span>';
                } elseif ($item->status == '0') {
                    return '<span class="badge badge-pill badge-danger">Ended</span>';
                }
            })
            ->rawColumns(['action','team', 'competition','opponentTeam','date','status'])
            ->make();
    }

    public function dataTablesMatch(){
        $data = $this->indexMatch();
        return $this->makeDataTablesMatch($data);
    }

    public function dataTablesPlayerStats(EventSchedule $schedule){
        $data = $schedule->playerMatchStats;
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                return '
                        <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item edit-player-stats" href="" id="'.$item->id.'"><span class="material-icons">edit</span> Edit Player Stats</a>
                            <a class="dropdown-item" href="' . route('player-managements.show', ['player_management'=>$item->id]) . '"><span class="material-icons">visibility</span> View Player</a>
                          </div>
                        </div>';
            })
            ->editColumn('name', function ($item) {
                return '
                        <div class="media flex-nowrap align-items-center"
                             style="white-space: nowrap;">
                            <div class="avatar avatar-sm mr-8pt">
                                <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->user->foto) . '" alt="profile-pic"/>
                            </div>
                            <div class="media-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex d-flex flex-column">
                                        <p class="mb-0"><strong class="js-lists-values-lead">'. $item->user->firstName .' '. $item->user->lastName .'</strong></p>
                                        <small class="js-lists-values-email text-50">' . $item->position->name . '</small>
                                    </div>
                                </div>

                            </div>
                        </div>';
            })
            ->editColumn('updated_at', function ($item) {
                $date = date('M d, Y h:i A', strtotime($item->updated_at));
                return $date;
            })
            ->rawColumns(['action','name','updated_at'])
            ->make();
    }

    public function show(EventSchedule $schedule){
        $totalParticipant = count($schedule->players) + count($schedule->coaches);

        $playerAttended = $schedule->players()
            ->where('attendanceStatus', 'Attended')
            ->get();

        $playerIllness = $schedule->players()
            ->where('attendanceStatus', 'Illness')
            ->get();
        $playerInjured = $schedule->players()
            ->where('attendanceStatus', 'Injured')
            ->get();
        $playerOther = $schedule->players()
            ->where('attendanceStatus', 'Other')
            ->get();
        $playerDidntAttend = count($playerIllness) + count($playerInjured) + count($playerOther);

        $coachAttended = $schedule->coaches()
            ->where('attendanceStatus', 'Attended')
            ->get();

        $coachIllness = $schedule->coaches()
            ->where('attendanceStatus', 'Illness')
            ->get();
        $coachInjured = $schedule->coaches()
            ->where('attendanceStatus', 'Injured')
            ->get();
        $coachOther = $schedule->coaches()
            ->where('attendanceStatus', 'Other')
            ->get();
        $coachDidntAttend = count($coachIllness) + count($coachInjured) + count($coachOther);

        $totalAttend = count($playerAttended) + count($coachAttended);
        $totalDidntAttend = $playerDidntAttend + $coachDidntAttend;
        $totalIllness = count($playerIllness) + count($coachIllness);
        $totalInjured = count($playerInjured) + count($coachInjured);
        $totalOthers = count($playerOther) + count($coachOther);

        $dataSchedule = $schedule;

        return compact('totalParticipant', 'totalAttend', 'totalDidntAttend', 'totalIllness', 'totalInjured', 'totalOthers', 'dataSchedule');
    }

    public function storeTraining(array $data, $userId){
        $data['userId'] = $userId;
        $data['eventType'] = 'Training';
        $data['status'] = '1';
        $schedule =  EventSchedule::create($data);

        $team = Team::with('players', 'coaches')->where('id', $data['teamId'])->first();

        $schedule->teams()->attach($data['teamId']);
        $schedule->players()->attach($team->players);
        $schedule->coaches()->attach($team->coaches);

        return $schedule;
    }


    public function storeMatch(array $data, $userId){
        $data['userId'] = $userId;
        $data['eventType'] = 'Match';
        $data['status'] = '1';
        $schedule =  EventSchedule::create($data);

        $team = Team::with('players', 'coaches')->where('id', $data['teamId'])->first();

        $schedule->teams()->attach($data['teamId']);
        $schedule->teams()->attach($data['opponentTeamId']);
        $schedule->players()->attach($team->players);
        $schedule->playerMatchStats()->attach($team->players, ['teamId' => $team->id]);
        $schedule->coaches()->attach($team->coaches);

        return $schedule;
    }

    public function updateTraining(array $data, EventSchedule $schedule){
        $schedule->update($data);

        if (array_key_exists('teamId', $data)){
            $team = Team::with('players', 'coaches')->where('id', $data['teamId'])->where('teamSide', 'Academy Team')->first();
            $schedule->teams()->sync($data['teamId']);
            $schedule->players()->sync($team->players);
            $schedule->coaches()->sync($team->coaches);
        }
        return $schedule;
    }

    public function updateMatch(array $data, EventSchedule $schedule){
        $schedule->update($data);

        $team = Team::with('players', 'coaches')->where('id', $data['teamId'])->where('teamSide', 'Academy Team')->first();
        $schedule->teams()->sync([$data['teamId'], $data['opponentTeamId']]);
        $schedule->players()->sync($team->players);
        $schedule->coaches()->sync($team->coaches);
        return $schedule;
    }

    public function activate(EventSchedule $schedule)
    {
        return $schedule->update(['status' => '1']);
    }

    public function deactivate(EventSchedule $schedule)
    {
        return $schedule->update(['status' => '0']);
    }

    public function endMatch(EventSchedule $schedule)
    {

        $academyTeamScore = $schedule->teams[0]->pivot->teamScore;
        $opponentTeamScore = $schedule->teams[1]->pivot->teamScore;

        $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['goalConceded'=> $opponentTeamScore]);
        $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, ['goalConceded'=> $academyTeamScore]);

        $academyTeamGoalsDifference = $academyTeamScore - $opponentTeamScore;
        $opponentTeamGoalsDifference = $opponentTeamScore - $academyTeamScore;

        if ($schedule->competition()->exists()){
            $groupDivision = $schedule->competition->groups()
                ->whereRelation('teams','teamId', $schedule->teams[0]->id)
                ->first();

            $academyTeam = $groupDivision->teams()->where('teamId', $schedule->teams[0]->id)->first();
            $opponentTeam = $groupDivision->teams()->where('teamId', $schedule->teams[0]->id)->first();
        }

        if ($academyTeamScore > $opponentTeamScore){
            $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['resultStatus'=> 'Win']);
            $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, ['resultStatus'=> 'Lose']);

            if ($schedule->competition()->exists()){
                $groupDivision->teams()
                    ->updateExistingPivot($schedule->teams[0]->id, [
                        'matchPlayed'=> $academyTeam->pivot->matchPlayed + 1,
                        'won'=> $academyTeam->pivot->won + 1,
                        'goalsFor'=> $academyTeam->pivot->goalsFor + $academyTeamScore,
                        'goalsAgaints'=> $academyTeam->pivot->goalsAgaints + $opponentTeamScore,
                        'goalsDifference'=> $academyTeam->pivot->goalsDifference + $academyTeamGoalsDifference,
                        'points'=> $academyTeam->pivot->points + 3,
                        'redCards'=> $academyTeam->pivot->redCards + $schedule->teams[0]->pivot->teamRedCards,
                        'yellowCards'=> $academyTeam->pivot->yellowCards + $schedule->teams[0]->pivot->teamYellowCards,
                    ]);

                $groupDivision->teams()
                    ->updateExistingPivot($schedule->teams[1]->id, [
                        'matchPlayed'=> $opponentTeam->pivot->matchPlayed + 1,
                        'lost'=> $opponentTeam->pivot->lost + 1,
                        'goalsFor'=> $opponentTeam->pivot->goalsFor + $opponentTeamScore,
                        'goalsAgaints'=> $opponentTeam->pivot->goalsAgaints + $academyTeamScore,
                        'goalsDifference'=> $opponentTeam->pivot->goalsDifference + $opponentTeamGoalsDifference,
                        'points'=> $opponentTeam->pivot->points + 0,
                        'redCards'=> $opponentTeam->pivot->redCards + $schedule->teams[1]->pivot->teamRedCards,
                        'yellowCards'=> $opponentTeam->pivot->yellowCards + $schedule->teams[1]->pivot->teamYellowCards,
                    ]);
            }

        } elseif ($academyTeamScore < $opponentTeamScore){
            $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, ['resultStatus'=> 'Win']);
            $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['resultStatus'=> 'Lose']);

            if ($schedule->competition()->exists()){
                $groupDivision->teams()
                    ->updateExistingPivot($schedule->teams[0]->id, [
                        'matchPlayed'=> $academyTeam->pivot->matchPlayed + 1,
                        'lost'=> $academyTeam->pivot->lost + 1,
                        'goalsFor'=> $academyTeam->pivot->goalsFor + $academyTeamScore,
                        'goalsAgaints'=> $academyTeam->pivot->goalsAgaints + $opponentTeamScore,
                        'goalsDifference'=> $academyTeam->pivot->goalsDifference + $academyTeamGoalsDifference,
                        'points'=> $academyTeam->pivot->points + 0,
                        'redCards'=> $academyTeam->pivot->redCards + $schedule->teams[0]->pivot->teamRedCards,
                        'yellowCards'=> $academyTeam->pivot->yellowCards + $schedule->teams[0]->pivot->teamYellowCards,
                    ]);

                $groupDivision->teams()
                    ->updateExistingPivot($schedule->teams[1]->id, [
                        'matchPlayed'=> $opponentTeam->pivot->matchPlayed + 1,
                        'won'=> $opponentTeam->pivot->won + 1,
                        'goalsFor'=> $opponentTeam->pivot->goalsFor + $opponentTeamScore,
                        'goalsAgaints'=> $opponentTeam->pivot->goalsAgaints + $academyTeamScore,
                        'goalsDifference'=> $opponentTeam->pivot->goalsDifference + $opponentTeamGoalsDifference,
                        'points'=> $opponentTeam->pivot->points + 3,
                        'redCards'=> $opponentTeam->pivot->redCards + $schedule->teams[1]->pivot->teamRedCards,
                        'yellowCards'=> $opponentTeam->pivot->yellowCards + $schedule->teams[1]->pivot->teamYellowCards,
                    ]);
            }

        }elseif ($academyTeamScore == $opponentTeamScore){
            $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, ['resultStatus'=> 'Draw']);
            $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['resultStatus'=> 'Draw']);

            if ($schedule->competition()->exists()){
                $groupDivision->teams()
                    ->updateExistingPivot($schedule->teams[0]->id, [
                        'matchPlayed'=> $academyTeam->pivot->matchPlayed + 1,
                        'drawn'=> $academyTeam->pivot->drawn + 1,
                        'goalsFor'=> $academyTeam->pivot->goalsFor + $academyTeamScore,
                        'goalsAgaints'=> $academyTeam->pivot->goalsAgaints + $opponentTeamScore,
                        'goalsDifference'=> $academyTeam->pivot->goalsDifference + $academyTeamGoalsDifference,
                        'points'=> $academyTeam->pivot->points + 1,
                        'redCards'=> $academyTeam->pivot->redCards + $schedule->teams[0]->pivot->teamRedCards,
                        'yellowCards'=> $academyTeam->pivot->yellowCards + $schedule->teams[0]->pivot->teamYellowCards,
                    ]);

                $groupDivision->teams()
                    ->updateExistingPivot($schedule->teams[1]->id, [
                        'matchPlayed'=> $opponentTeam->pivot->matchPlayed + 1,
                        'drawn'=> $opponentTeam->pivot->drawn + 1,
                        'goalsFor'=> $opponentTeam->pivot->goalsFor + $opponentTeamScore,
                        'goalsAgaints'=> $opponentTeam->pivot->goalsAgaints + $academyTeamScore,
                        'goalsDifference'=> $opponentTeam->pivot->goalsDifference + $opponentTeamGoalsDifference,
                        'points'=> $opponentTeam->pivot->points + 1,
                        'redCards'=> $opponentTeam->pivot->redCards + $schedule->teams[1]->pivot->teamRedCards,
                        'yellowCards'=> $opponentTeam->pivot->yellowCards + $schedule->teams[1]->pivot->teamYellowCards,
                    ]);
            }
        }

        if ($academyTeamScore == 0){
            $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['cleanSheets'=> 1]);
        }

        if ($opponentTeamScore == 0){
            $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, ['cleanSheets'=> 1]);
        }

        return $schedule->update(['status' => '0']);
    }


    public function getPlayerAttendance(EventSchedule $schedule, Player $player)
    {
        return $schedule->players()->find($player->id);
    }

    public function getCoachAttendance(EventSchedule $schedule, Coach $coach)
    {
        return $schedule->coaches()->find($coach->id);
    }

    public function updatePlayerAttendanceStatus($data, EventSchedule $schedule, Player $player){
        return $schedule->players()->updateExistingPivot($player->id, ['attendanceStatus'=> $data['attendanceStatus'], 'note' => $data['note']]);
    }
    public function updateCoachAttendanceStatus($data, EventSchedule $schedule, Coach $coach){
        return $schedule->coaches()->updateExistingPivot($coach->id, ['attendanceStatus'=> $data['attendanceStatus'], 'note' => $data['note']]);
    }

    public function createNote($data, EventSchedule $schedule){
        $data['scheduleId'] = $schedule->id;
        return ScheduleNote::create($data);
    }

    public function updateNote($data, EventSchedule $schedule, ScheduleNote $note){
        return $note->update($data);
    }
    public function destroyNote(EventSchedule $schedule, ScheduleNote $note)
    {
        return $note->delete();
    }

    public function storeMatchScorer($data, EventSchedule $schedule)
    {
        $data['eventId'] = $schedule->id;
        $data['isOwnGoal'] = '0';
        MatchScore::create($data);
        $player = $schedule->playerMatchStats()->find($data['playerId']);
        $assistPlayer = $schedule->playerMatchStats()->find($data['assistPlayerId']);

        $playerGoal = $player->pivot->goals + 1;
        $playerAssist = $assistPlayer->pivot->assists + 1;
        $teamScore = $schedule->teams[0]->pivot->teamScore + 1;

        $schedule->playerMatchStats()->updateExistingPivot($data['playerId'], ['goals' => $playerGoal]);
        $schedule->playerMatchStats()->updateExistingPivot($data['assistPlayerId'], ['assists' => $playerAssist]);
        $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['teamScore' => $teamScore]);
        return $schedule;
    }

    public function destroyMatchScorer(EventSchedule $schedule, MatchScore $scorer)
    {
        $player = $schedule->playerMatchStats()->find($scorer->playerId);
        $assistPlayer = $schedule->playerMatchStats()->find($scorer->assistPlayerId);

        $teamScore = $schedule->teams[0]->pivot->teamScore - 1;
        $playerGoal = $player->pivot->goals - 1;
        $playerAssist = $assistPlayer->pivot->assists - 1;

        $schedule->playerMatchStats()->updateExistingPivot($scorer->playerId, ['goals' => $playerGoal]);
        $schedule->playerMatchStats()->updateExistingPivot($scorer->assistPlayerId, ['assists' => $playerAssist]);
        $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['teamScore' => $teamScore]);

        return $scorer->delete();
    }

    public function storeOwnGoal($data, EventSchedule $schedule)
    {
        $data['eventId'] = $schedule->id;
        $data['isOwnGoal'] = '1';
        MatchScore::create($data);
        $player = $schedule->playerMatchStats()->find($data['playerId']);

        $playerGoal = $player->pivot->ownGoal + 1;
        $teamScore = $schedule->teams[1]->pivot->teamScore + 1;

        $schedule->playerMatchStats()->updateExistingPivot($data['playerId'], ['ownGoal' => $playerGoal]);
        $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, ['teamScore' => $teamScore]);
        return $schedule;
    }

    public function destroyOwnGoal(EventSchedule $schedule, MatchScore $scorer)
    {
        $player = $schedule->playerMatchStats()->find($scorer->playerId);

        $teamScore = $schedule->teams[1]->pivot->teamScore - 1;
        $playerGoal = $player->pivot->ownGoal - 1;

        $schedule->playerMatchStats()->updateExistingPivot($scorer->playerId, ['ownGoal' => $playerGoal]);
        $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, ['teamScore' => $teamScore]);

        return $scorer->delete();
    }

    public function updateMatchStats(array $data, EventSchedule $schedule)
    {
        $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, [
                'teamPossesion' => $data['teamAPossession'],
                'teamShotOnTarget' => $data['teamAShotOnTarget'],
                'teamShots' => $data['teamAShots'],
                'teamTouches' => $data['teamATouches'],
                'teamTackles' => $data['teamATackles'],
                'teamClearances' => $data['teamAClearances'],
                'teamCorners' => $data['teamACorners'],
                'teamOffsides' => $data['teamAOffsides'],
                'teamYellowCards' => $data['teamAYellowCards'],
                'teamRedCards' => $data['teamARedCards'],
                'teamFoulsConceded' => $data['teamAFoulsConceded'],
                'teamPasses' => $data['teamAPasses'],
            ]);

        $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, [
            'teamScore' => $data['teamBTeamScore'],
            'teamOwnGoal' => $data['teamBOwnGoal'],
            'teamPossesion' => $data['teamBPossession'],
            'teamShotOnTarget' => $data['teamBShotOnTarget'],
            'teamShots' => $data['teamBShots'],
            'teamTouches' => $data['teamBTouches'],
            'teamTackles' => $data['teamBTackles'],
            'teamClearances' => $data['teamBClearances'],
            'teamCorners' => $data['teamBCorners'],
            'teamOffsides' => $data['teamBOffsides'],
            'teamYellowCards' => $data['teamBYellowCards'],
            'teamRedCards' => $data['teamBRedCards'],
            'teamFoulsConceded' => $data['teamBFoulsConceded'],
            'teamPasses' => $data['teamBPasses'],
        ]);

        return $schedule;
    }

    public function getPlayerStats(EventSchedule $schedule, Player $player)
    {
        $data = $schedule->playerMatchStats()->find($player->id);
        $playerData = $data->user;
        $statsData = $data->pivot;
        return compact('playerData', 'statsData');
    }

    public function updatePlayerStats(array $data, EventSchedule $schedule, Player $player)
    {
//        $schedule->playerMatchStats()->updateExistingPivot($player->id, [
//            'minutesPlayed' => $data['minutesPlayed'],
//            'shots' => $data['shots'],
//            'passes' => $data['passes'],
//            'fouls' => $data['fouls'],
//            'yellowCards' => $data['yellowCards'],
//            'redCards' => $data['redCards'],
//            'saves' => $data['saves'],
//        ]);
//        dd($schedule);

        return $schedule->playerMatchStats()->updateExistingPivot($player->id, $data);
    }

    public function destroy(EventSchedule $schedule)
    {
        $schedule->teams()->detach();
        $schedule->players()->detach();
        $schedule->coaches()->detach();
        $schedule->delete();
        return $schedule;
    }
}
