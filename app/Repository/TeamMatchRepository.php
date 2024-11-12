<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\Player;
use App\Models\Team;
use App\Models\TeamMatch;
use Illuminate\Database\Eloquent\Builder;

class TeamMatchRepository
{
    protected TeamMatch $teamMatch;
    public function __construct(TeamMatch $teamMatch)
    {
        $this->teamMatch = $teamMatch;
    }

    public function getAll()
    {
        return $this->teamMatch->all();
    }

    public function getTeamsStats(Team $team, $teamSide = 'Academy Team', $startDate = null, $endDate = null, $stats = null, $results = null)
    {
        $query = $this->teamMatch->whereHas('team', function($q) use ($team, $teamSide) {
                $q->where('teamSide', $teamSide);
                $q->where('teamId', $team->id);
            });

        if ($startDate != null && $endDate != null){
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($stats){
            return $query->sum($stats);
        }elseif ($results){
            return $query->where('resultStatus', $results)->count();
        }
    }

    public function find($id)
    {
        return $this->teamMatch->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->teamMatch->create($data);
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
