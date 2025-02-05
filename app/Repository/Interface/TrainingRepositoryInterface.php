<?php

namespace App\Repository\Interface;

use App\Models\Player;
use App\Models\Training;
use Illuminate\Database\Eloquent\Collection;

interface TrainingRepositoryInterface
{
    public function getAll(
        array                                    $relations = ['team'],
        Collection $teams = null,
        Player                                   $player = null,
                                                 $status = null,
                                                 $take = null,
                                                 $startDate = null,
                                                 $endDate = null,
        bool                                     $beforeStartDate = false,
        bool                                     $beforeEndDate = false,
                                                 $reminderNotified = null,
        string                                   $orderBy = 'date',
        string                                   $orderDirection = 'desc',
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
        string $startDate = null,
        string $endDate = null,
        Collection $teams = null
    );

    public function countAttendanceByStatus(
        string $startDate = null,
        string $endDate = null,
        Collection $teams = null
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
