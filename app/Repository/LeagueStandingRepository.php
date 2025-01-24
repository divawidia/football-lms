<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\Competition;
use App\Models\MatchModel;
use App\Models\LeagueStanding;
use App\Models\Team;
use App\Repository\Interface\LeagueStandingRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class LeagueStandingRepository implements LeagueStandingRepositoryInterface
{
    protected LeagueStanding $leagueStanding;
    public function __construct(LeagueStanding $leagueStanding)
    {
        $this->leagueStanding = $leagueStanding;
    }

    public function getAll(Competition $competition = null, Team $team = null)
    {
        $query = $this->leagueStanding->with('team');
        if ($competition){
            $query->where('competitionId', $competition->id);
        }
        if ($team) {
            $query->where('teamId', $team->id);
        }
        return $query->get();
    }

    public function create(array $data, Competition $competition)
    {
        return $competition->standings()->create([
            'teamId' => $data['teams'],
        ]);
    }

    public function update(LeagueStanding $standing, array $data)
    {
        return $standing->update($data);
    }

    public function delete(LeagueStanding $standing)
    {
        $standing->delete();
        return $standing;
    }
}
