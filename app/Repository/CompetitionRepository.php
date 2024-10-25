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

    public function getAll()
    {
        return $this->competition->with('groups.teams')->get();
    }
    public function getCoachCompetition($teams)
    {
        return $this->competition->with('groups.teams')
            ->whereHas('teams', function($q) use ($teams){
                $q->where('teamId', $teams[0]->id);

                // if teams are more than 1 then iterate more
                if (count($teams)>1){
                    for ($i = 1; $i < count($teams); $i++){
                        $q->orWhere('teamId', $teams[$i]->id);
                    }
                }
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
