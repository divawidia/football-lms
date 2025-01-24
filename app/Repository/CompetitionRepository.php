<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\Competition;
use App\Models\MatchModel;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;

class CompetitionRepository
{
    protected Competition $competition;
    public function __construct(Competition $competition)
    {
        $this->competition = $competition;
    }

    public function getAll($withRelation = [], $teams = null, $status = null)
    {
        $query = $this->competition->with($withRelation);
        if ($teams){
            $teamIds = collect($teams)->pluck('id')->all();
            $query->whereHas('matches.teams', function($q) use ($teamIds){
                $q->whereIn('teamId', $teamIds);
            });
        }
        if ($status){
            $query->where('status', $status);
        }
        return $query->get();
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
