<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\Team;
use App\Repository\Interface\CoachRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

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

    public function getAll($relations = [], $certification = null, $specializations = null, $team = null, $status = null, $columns = ['*']): Collection
    {
        $query = $this->coach->with($relations);
        if ($team) {
            $query->whereRelation('teams', 'teamId', $team);
        }
        if ($certification) {
            $query->where('certificationId', $certification);
        }
        if ($specializations) {
            $query->where('specializationId', $specializations);
        }
        if ($status != null) {
            $query->whereRelation('user','status', $status);
        }
        return $query->get($columns);
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

    public function matchStats(Coach $coach, $startDate = null, $endDate = null, $result = null, $retrievalMethod = 'count', $column = null)
    {
        $query = $coach->coachMatchStats();

        if ($startDate && $endDate) {
            $query->whereBetween('coach_match_stats.created_at', [$startDate, $endDate]);
        }

        if (!$result) {
            $query->where('coach_match_stats.resultStatus', $result);
        }

        if ($retrievalMethod == 'count') {
            return $query->count();
        } elseif ($retrievalMethod == 'sum') {
            return $query->sum($column);
        } else {
            return $query->get($column);
        }
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
