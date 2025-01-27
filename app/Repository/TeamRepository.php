<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\Competition;
use App\Models\GroupDivision;
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

    public function getAll()
    {
        return $this->team->all();
    }

    public function getByTeamside($teamSide, $exceptTeamId=null)
    {
        $query = $this->team->where('teamSide', $teamSide);
        if ($exceptTeamId != null){
            $query->where('id', '!=', $exceptTeamId);
        }
        return $query->get();
    }

    public function getInArray($ids)
    {
        return $this->team->with('players', 'coaches')
            ->whereIn('id', $ids)
            ->get();
    }

    public function getTeamsHaventJoinedByCoach(Coach $coach)
    {
        return $this->team->where('teamSide', 'Academy Team')
            ->whereDoesntHave('coaches', function (Builder $query) use ($coach) {
                $query->where('coachId', $coach->id);
            })->get();
    }
    public function getTeamsHaventJoinedByPLayer(Player $player)
    {
        return $this->team->where('teamSide', 'Academy Team')
            ->whereDoesntHave('players', function (Builder $query) use ($player) {
                $query->where('playerId', $player->id);
            })->get();
    }

    public function getTeamsHaventJoinedCompetition(Competition $competition, $teamSide)
    {
        return $this->team->where('teamSide', $teamSide)
            ->whereDoesntHave('divisions', function (Builder $query) use ($competition) {
                $query->where('competitionId', $competition->id);
            })->get();
    }

    public function getTeamsJoinedGroupDivision(GroupDivision $groupDivision, $teamSide, $exceptTeamId = null)
    {
        $query = $this->team->where('teamSide', $teamSide)
            ->whereHas('divisions', function (Builder $query) use ($groupDivision) {
                $query->where('divisionId', $groupDivision->id);
            });
        if ($exceptTeamId != null){
            return $query->where('id', '!=', $exceptTeamId)->get();
        } else {
            return $query->get();
        }
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
