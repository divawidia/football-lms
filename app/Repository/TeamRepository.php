<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\Competition;
use App\Models\Player;
use App\Models\Team;
use App\Repository\Interface\TeamRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class TeamRepository implements TeamRepositoryInterface
{
    protected Team $team;
    public function __construct(Team $team)
    {
        $this->team = $team;
    }

    public function getAll($withRelation = [], $exceptTeamId = null, $exceptCoach = null, $exceptPLayer = null, $columns = ['*'])
    {
        $query = $this->team->with($withRelation);
        if (!is_null($exceptTeamId)) {
            $query->where('id', '!=', $exceptTeamId);
        }
        if ($exceptCoach) {
            $query->whereDoesntHave('coaches', function (Builder $query) use ($exceptCoach) {
                $query->where('coachId', $exceptCoach->id);
            });
        }
        if ($exceptPLayer) {
            $query->whereDoesntHave('players', function (Builder $query) use ($exceptPLayer) {
                $query->where('playerId', $exceptPLayer->id);
            });
        }
        return $query->get($columns);
    }

    public function getByTeamside($teamSide, $exceptTeamId=null)
    {
        $query = $this->team->where('teamSide', $teamSide);
        if ($exceptTeamId != null){
            $query->where('id', '!=', $exceptTeamId);
        }
        return $query->get();
    }

    public function getJoinedCompetition(Competition $competition)
    {
        $teams = [];
        foreach ($competition->groups as $group){
            if (count($group->teams) > 0) {
                $team = $group->teams->where('teamSide', 'Academy Team')->first();
                $teams[] = $team;
            }
        }
        return $teams;
    }

    public function find($id)
    {
        return $this->team->findOrFail($id);
    }

    public function whereId($id)
    {
        return $this->team->where('id', $id)->get();
    }

    public function create(array $data)
    {
        return $this->team->create($data);
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
