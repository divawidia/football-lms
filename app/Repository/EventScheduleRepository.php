<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\EventSchedule;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;

class EventScheduleRepository
{
    protected EventSchedule $eventSchedule;
    public function __construct(EventSchedule $eventSchedule)
    {
        $this->eventSchedule = $eventSchedule;
    }

    public function getAll()
    {
        return $this->eventSchedule->all();
    }

    public function getEvent($eventType, $status, $take = null)
    {
        $query = $this->eventSchedule->with('teams', 'competition')
            ->where('eventType', $eventType)
            ->where('status', $status);
        if ($take){
            $query->take($take);
        }
        return $query->orderBy('date')->get();
    }

    public function getEventByModel($model, $eventType, $status, $take = null, $sortDateDirection = 'asc')
    {
        $query = $model->schedules()->with('teams', 'competition')
            ->where('eventType', $eventType)
            ->where('status', $status);
        if ($take){
            $query->take($take);
        }
        return $query->orderBy('date', $sortDateDirection)->get();
    }

    public function playerLatestEvent(Player $player, $eventType, $take = 2)
    {
        return $player->schedules()
            ->where('eventType', $eventType)
            ->where('status', '0')
            ->take($take)
            ->orderBy('date', 'desc')
            ->get();
    }

    public function getTeamsMatchPlayed(Team $team, $teamSide = 'Academy Team', $startDate = null, $endDate = null)
    {
        $query = $this->eventSchedule->whereHas('teams', function($q) use ($team, $teamSide) {
                $q->where('teamSide', $teamSide);
                $q->where('teamId', $team->id);
            })
            ->where('status', '0')
            ->where('eventType', 'Match');

        if ($startDate != null && $endDate != null){
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return $query->count();
    }

    public function getTeamsEvents(Team $team, $eventType, $status, $latest = false, $take = null)
    {
        $query = $team->schedules()
            ->where('eventType', $eventType)
            ->where('status', $status);
        if ($latest){
            $query->latest('date');
        }
        if ($take != null){
            $query->take(2);
        }

        return $query->get();
    }

    public function find($id)
    {
        return $this->eventSchedule->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->eventSchedule->create($data);
    }

    public function update($id, array $data)
    {
        $post = $this->find($id);
        $post->update($data);
        return $post;
    }

    public function updateStatus(EventSchedule $schedule, $status)
    {
        return $schedule->update(['status' => $status]);
    }

    public function delete($id)
    {
        $post = $this->find($id);
        $post->delete();
        return $post;
    }
}
