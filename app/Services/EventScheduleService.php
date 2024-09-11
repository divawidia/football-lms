<?php

namespace App\Services;

use App\Models\Coach;
use App\Models\EventSchedule;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class EventScheduleService extends Service
{
    public function index(): Collection
    {
        return EventSchedule::all();
    }

    public function storeTraining(array $data, $userId){
        $data['userId'] = $userId;
        $data['eventType'] = 'Training';
        $data['status'] = '1';
        $schedule =  EventSchedule::create($data);

        $team = Team::with('players', 'coaches')->where('id', $data['teamId'])->get();

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

    public function updateNote($data, EventSchedule $schedule){
        $schedule->update(['note' => $data['note']]);
    }

    public function destroyNote(EventSchedule $schedule){
        $schedule->update(['note' => '']);
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
