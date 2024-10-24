<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
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

    public function getAcademyTeams()
    {
        return $this->team->where('teamSide', 'Academy Team')->get();
    }

    public function getOpponentTeams()
    {
        return $this->team->where('teamSide', 'Opponent Team')->get();
    }

    public function getTeamsHaventJoinedByCoach(Coach $coach)
    {
        return $this->team->where('teamSide', 'Academy Team')
            ->whereDoesntHave('coaches', function (Builder $query) use ($coach) {
                $query->where('coachId', $coach->id);
            })->get();
    }

    public function find($id)
    {
        return $this->post->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->post->create($data);
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
