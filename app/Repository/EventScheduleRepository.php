<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\EventSchedule;
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

    public function getMatchHistories()
    {
        return $this->eventSchedule->with('teams', 'competition')
            ->where('eventType', 'Match')
            ->where('status', '0')
            ->get();
    }

    public function getCoachMatchHistories(Coach $coach)
    {
        return $coach->schedules()->with('teams', 'competition')
            ->where('eventType', 'Match')
            ->where('status', '0')
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
