<?php

namespace App\Repository\Interface;

use App\Models\Team;
use App\Models\User;

interface CoachRepositoryInterface
{
    /**
     * Retrieve all coaches with related users and teams.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll();

    /**
     * Get coaches not currently assigned to a specific team.
     *
     * @param Team $team
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCoachNotJoinSpecificTeam(Team $team);

    /**
     * Retrieve all coach specializations.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllCoachSpecialization();

    /**
     * Retrieve all coach certifications.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllCoachCertification();

    /**
     * Create a new coach record.
     *
     * @param array $data
     * @return Coach
     */
    public function create(array $data);
}
