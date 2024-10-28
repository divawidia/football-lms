<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;

class TeamRepository
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

    public function getByTeamside($teamSide)
    {
        return $this->team->where('teamSide', $teamSide)->get();
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

    public function getCoachManagedTeams(Coach $coach){
        return $coach->teams;
    }

    public function find($id)
    {
        return $this->team->findOrFail($id);
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
