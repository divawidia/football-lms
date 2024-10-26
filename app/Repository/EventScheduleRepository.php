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

    public function endedMatch()
    {
        return $this->eventSchedule->with('teams', 'competition')
            ->where('eventType', 'Match')
            ->where('status', '0');
    }
    public function endedCoachMatch(Coach $coach)
    {
        return $coach->schedules()->with('teams', 'competition')
            ->where('eventType', 'Match')
            ->where('status', '0');
    }


    public function getMatchHistories()
    {
        return $this->endedMatch()->get();
    }

    public function getCoachMatchHistories(Coach $coach)
    {
        return $this->endedCoachMatch($coach)->get();
    }

    public function getLatestMatch()
    {
        return $this->endedMatch()->take(2)->get();
    }
    public function getCoachLatestMatch(Coach $coach)
    {
        return $this->endedCoachMatch($coach)->take(2)->get();
    }

    public function playerUpcomingEvent(Player $player, $eventType, $take = 2)
    {
        return $player->schedules()
            ->where('eventType', $eventType)
            ->where('status', '1')
            ->take($take)
            ->get();
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

    public function delete($id)
    {
        $post = $this->find($id);
        $post->delete();
        return $post;
    }
}
