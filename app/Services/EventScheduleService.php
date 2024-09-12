<?php

namespace App\Services;

use App\Models\Coach;
use App\Models\EventSchedule;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class EventScheduleService extends Service
{
    public function indexMatch(): Collection
    {
        return EventSchedule::with('teams')->where('eventType', 'Match')->get();
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

    public function dataTablesTraining(){
        $data = $this->indexTraining();
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                if ($item->status == '1') {
                    $statusButton = '<form action="' . route('deactivate-training', $item->id) . '" method="POST">
                                                ' . method_field("PATCH") . '
                                                ' . csrf_field() . '
                                                <button type="submit" class="dropdown-item">
                                                    <span class="material-icons">block</span> Deactivate Competition
                                                </button>
                                            </form>';
                } else {
                    $statusButton = '<form action="' . route('activate-training', $item->id) . '" method="POST">
                                                ' . method_field("PATCH") . '
                                                ' . csrf_field() . '
                                                <button type="submit" class="dropdown-item">
                                                    <span class="material-icons">check_circle</span> Activate Competition
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
                            <a class="dropdown-item" href="' . route('training-schedules.edit', $item->id) . '"><span class="material-icons">edit</span> Edit Competition</a>
                            <a class="dropdown-item" href="' . route('training-schedules.show', $item->id) . '"><span class="material-icons">visibility</span> View Competition</a>
                            ' . $statusButton . '
                            <button type="button" class="dropdown-item delete" id="' . $item->id . '">
                                <span class="material-icons">delete</span> Delete Competition
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
                    return '<span class="badge badge-pill badge-success">Aktif</span>';
                } elseif ($item->status == '0') {
                    return '<span class="badge badge-pill badge-danger">Non Aktif</span>';
                }
            })
            ->rawColumns(['action','team','date','status'])
            ->make();
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

        $team = Team::with('players', 'coaches')->where('id', $data['teamId'])->where('teamSide', 'Academy Team')->get();

        $schedule->teams()->attach($data['teamId']);
        $schedule->players()->attach($team->players);
        $schedule->coaches()->attach($team->coaches);
        return $schedule;
    }

    public function update(array $data, EventSchedule $schedule){
        $schedule->update($data);

        if (array_key_exists('teamId', $data)){
            $team = Team::with('players', 'coaches')->where('id', $data['teamId'])->where('teamSide', 'Academy Team')->get();
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

    public function updatePlayerAttendanceStatus($data, EventSchedule $schedule, Player $player){
        return $schedule->players()->updateExistingPivot($player->id, ['attendanceStatus'=> $data['attendanceStatus'], 'note' => $data['note']]);
    }
    public function updateCoachAttendanceStatus($data, EventSchedule $schedule, Coach $coaches){
        return $schedule->coaches()->updateExistingPivot($coaches->id, ['attendanceStatus'=> $data['attendanceStatus'], 'note' => $data['note']]);
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
