<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\Competition;
use App\Models\EventSchedule;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;

class CompetitionRepository
{
    protected Competition $competition;
    public function __construct(Competition $competition)
    {
        $this->competition = $competition;
    }

    public function getAll($status = null)
    {
        $query = $this->competition->with('groups.teams');
        if ($status){
            $query->where('status', $status);
        }
        return $query->get();
    }
    public function getByTeams($teams)
    {
        $teamIds = collect($teams)->pluck('id')->all();
        return $this->competition->with('groups.teams')
            ->whereHas('groups.teams', function($q) use ($teamIds){
                $q->whereIn('teamId', $teamIds);
            })->get();
    }

    public function find($id)
    {
        return $this->competition->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->competition->create($data);
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
