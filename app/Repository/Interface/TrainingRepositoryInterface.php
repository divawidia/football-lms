<?php

namespace App\Repository\Interface;

use App\Models\Player;
use App\Models\Team;
use App\Models\Training;
use Illuminate\Support\Collection;

interface TrainingRepositoryInterface
{
    public function getAll(
        array $relations = ['team'],
        ?Team $team = null,
        Player $player = null,
              $status = null,
              $take = null,
              $startDate = null,
              $endDate = null,
        bool $beforeStartDate = false,
        bool $beforeEndDate = false,
              $reminderNotified = null,
        string $orderBy = 'date',
        string $orderDirection = 'desc',
        array $columns = ['*'],
        $retrievalMethod = 'all'
    );

    public function getByRelation(
        $relation,
        array $withRelation = [],
        $status = null,
        $startDate = null,
        $endDate = null,
        $take = null,
        string $orderBy = 'date',
        string $orderDirection = 'asc',
        array $column = ['*']
    );

    public function getAttendanceTrend(
        $startDate = null,
        $endDate = null,
        $teams = null
    );

    public function countAttendanceByStatus(
        $startDate = null,
        $endDate = null,
        $teams = null
    );

    public function playerAttendance(
        Player $player,
               $status = null,
               $startDate = null,
               $endDate = null
    ): int;

    public function getRelationData(Training $training, $relation, $with = null, $attendanceStatus = null, $teamId = null, $playerId = null, $exceptPlayerId= null, $retrieveType = 'single');

    public function create(array $data);

    public function find($id);
}
