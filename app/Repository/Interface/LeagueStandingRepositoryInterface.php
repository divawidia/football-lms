<?php

namespace App\Repository\Interface;

use App\Models\Competition;
use App\Models\LeagueStanding;
use App\Models\Team;
use App\Models\User;

interface LeagueStandingRepositoryInterface
{
    public function getAll(Competition $competition = null);
    public function create(array $data, Competition $competition);
    public function update(LeagueStanding $standing, array $data);

    public function delete(LeagueStanding $standing);
}
