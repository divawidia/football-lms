<?php

namespace App\Repository\Interface;

use App\Models\Coach;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface CoachRepositoryInterface
{
    /**
     * Retrieve all coaches with related users and teams.
     *
     * @return Collection
     */
    public function getAll($relations = [], $certification = null, $specializations = null, $team = null, $status = null, $columns = ['*']): Collection;

    /**
     * Get coaches not currently assigned to a specific team.
     *
     * @param Team $team
     * @return Collection
     */
    public function getCoachNotJoinSpecificTeam(Team $team);

    /**
     * Retrieve all coach specializations.
     *
     * @return Collection
     */
    public function getAllCoachSpecialization();

    /**
     * Retrieve all coach certifications.
     *
     * @return Collection
     */
    public function getAllCoachCertification();

    public function matchStats(Coach $coach, $startDate = null, $endDate = null, $result = null, $retrievalMethod = 'count', $column = null);

    /**
     * Create a new coach record.
     *
     * @param array $data
     * @return Coach
     */
    public function create(array $data);
}
