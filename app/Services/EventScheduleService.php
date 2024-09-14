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
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class EventScheduleService extends Service
{
    public function indexMatch(): Collection
    {
        return EventSchedule::with('teams', 'competition')->where('eventType', 'Match')->get();
    }
    public function indexTraining(): Collection
    {
        return EventSchedule::with('teams')->where('eventType', 'Training')->get();
    }

    public function getAcademyTeams(){
        return Team::where('teamSide', 'Academy Team')->get();
    }

    public function trainingCalendar(){
        $trainings = $this->indexTraining();
        $events = [];
        foreach ($trainings as $training) {
            $events[] = [
                'id' => $training->id,
                'title' => $training->eventName.' - '.$training->teams[0]->teamName,
                'start' => $training->date.' '.$training->startTime,
                'end' => $training->date.' '.$training->endTime,
                'className' => 'bg-warning'
            ];
        }
        return $events;
    }

    public function matchCalendar(){
        $matches = $this->indexMatch();
        $events = [];
        foreach ($matches as $match) {
            $events[] = [
                'id' => $match->id,
                'title' => $match->eventName.' - '.$match->teams[0]->teamName .' Vs. '.$match->teams[1]->teamName,
                'start' => $match->date.' '.$match->startTime,
                'end' => $match->date.' '.$match->endTime,
                'className' => 'bg-primary'
            ];
        }
        return $events;
    }

    public function dataTablesTraining(){
        $data = $this->indexTraining();
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                if ($item->status == '1') {
                    $statusButton = '<form action="' . route('deactivate-training', $item->id) . '" method="POST">
                                                ' . method_field("PATCH") . '
                                                ' . csrf_field() . '
                                                <button type="submit" class="dropdown-item">
                                                    <span class="material-icons">block</span> Deactivate Schedule
                                                </button>
                                            </form>';
                } else {
                    $statusButton = '<form action="' . route('activate-training', $item->id) . '" method="POST">
                                                ' . method_field("PATCH") . '
                                                ' . csrf_field() . '
                                                <button type="submit" class="dropdown-item">
                                                    <span class="material-icons">check_circle</span> Activate Schedule
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

    public function dataTablesMatch(){
        $data = $this->indexMatch();
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                if ($item->status == '1') {
                    $statusButton = '<form action="' . route('deactivate-match', $item->id) . '" method="POST">
                                                ' . method_field("PATCH") . '
                                                ' . csrf_field() . '
                                                <button type="submit" class="dropdown-item">
                                                    <span class="material-icons">block</span> Deactivate Schedule
                                                </button>
                                            </form>';
                } else {
                    $statusButton = '<form action="' . route('activate-match', $item->id) . '" method="POST">
                                                ' . method_field("PATCH") . '
                                                ' . csrf_field() . '
                                                <button type="submit" class="dropdown-item">
                                                    <span class="material-icons">check_circle</span> Activate Schedule
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
                            <a class="dropdown-item" href="' . route('match-schedules.edit', $item->id) . '"><span class="material-icons">edit</span> Edit Player Stats</a>
                            <a class="dropdown-item" href="' . route('match-schedules.show', $item->id) . '"><span class="material-icons">visibility</span> View Player</a>
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
        $playerDidntAttend = $schedule->players()
            ->where('attendanceStatus', 'Illness')
            ->orWhere('attendanceStatus', 'Injured')
            ->orWhere('attendanceStatus', 'Other')
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
        $coachAttended = $schedule->coaches()
            ->where('attendanceStatus', 'Attended')
            ->get();
        $coachDidntAttend = $schedule->coaches()
            ->where('attendanceStatus', 'Illness')
            ->where('attendanceStatus', 'Injured')
            ->where('attendanceStatus', 'Other')
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

        $totalAttend = count($playerAttended) + count($coachAttended);
        $totalDidntAttend = count($playerDidntAttend) + count($coachDidntAttend);
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
        $schedule->playerMatchStats()->attach($team->players);
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
    public function activate(EventSchedule $schedule)
    {
        return $schedule->update(['status' => '1']);
    }

    public function deactivate(EventSchedule $schedule)
    {
        return $schedule->update(['status' => '0']);
    }

    public function getPlayerAttendance(EventSchedule $schedule, Player $player)
    {
        return Player::with('schedules', 'user')
            ->whereRelation('schedules', 'scheduleId', $schedule->id)
            ->findOrFail($player->id);
    }

    public function getCoachAttendance(EventSchedule $schedule, Coach $coach)
    {
        return Coach::with('schedules', 'user')
            ->whereRelation('schedules', 'scheduleId', $schedule->id)
            ->findOrFail($coach->id);
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
        $matchScore = MatchScore::create($data);
        $teamScore = $schedule->teams[0]->teamScore + 1;
        $player = $schedule->playerMatchStats()->find($data['playerId']);
        $assistPlayer = $schedule->playerMatchStats()->find($data['assistPlayerId']);
        $playerGoal = $player->pivot->goals + 1;
//        dd($playerGoal);
        $playerAssist = $assistPlayer->pivot->assists + 1;
        $schedule->playerMatchStats()->updateExistingPivot($data['playerId'], ['goals' => $playerGoal]);
        $schedule->playerMatchStats()->updateExistingPivot($data['assistPlayerId'], ['assists' => $playerAssist]);
        $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['teamScore' => $teamScore]);
        return $schedule;
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
