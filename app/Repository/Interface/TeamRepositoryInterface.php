<?php

namespace App\Repository\Interface;

use App\Models\Coach;
use App\Models\Competition;
use App\Models\Player;

interface TeamRepositoryInterface
{
    public function getAll($withRelation = [], $exceptTeamId = null, $exceptCoach = null, $exceptPLayer = null, $columns = ['*'], $status = '1');

    public function getByTeamside($teamSide, $exceptTeamId = null);

    public function find($id);

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);
}
