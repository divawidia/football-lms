<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\Team;
use App\Repository\Interface\CoachRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class CoachRepository implements CoachRepositoryInterface
{
    protected Coach $coach;
    protected CoachSpecialization $coachSpecialization;
    protected CoachCertification $coachCertification;

    public function __construct(Coach $coach, CoachSpecialization $coachSpecialization, CoachCertification $coachCertification)
    {
        $this->coach = $coach;
        $this->coachSpecialization = $coachSpecialization;
        $this->coachCertification = $coachCertification;
    }

    public function getAll()
    {
        return $this->coach->with('user', 'teams')->get();
    }

    public function getCoachNotJoinSpecificTeam(Team $team)
    {
        return $this->coach->with('user')
            ->whereDoesntHave('teams', function (Builder $query) use ($team){
                $query->where('teamId', $team->id);
            })
            ->get();
    }

    public function getAllCoachSpecialization()
    {
        return $this->coachSpecialization->all();
    }
    public function getAllCoachCertification()
    {
        return $this->coachCertification->all();
    }

    public function create(array $data)
    {
        $coach = $this->coach->create($data);
        if (array_key_exists('team',$data)){
            $coach->teams()->attach($data['team']);
        }
        return $coach;
    }
}
