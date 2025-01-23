<?php

namespace App\Repository;

use App\Models\Player;
use App\Models\Team;
use App\Models\Training;
use App\Repository\Interface\TrainingRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class TrainingRepository implements TrainingRepositoryInterface
{
    protected Training $training;
    public function __construct(Training $training)
    {
        $this->training = $training;
    }

    public function getAll($relations = ['teams'], Team $team = null, Player $player = null, $status = null, $take = null, $startDate = null, $endDate = null, $beforeStartDate = false, $beforeEndDate = false, $reminderNotified = null, $orderBy = 'date', $orderDirection = 'desc', $columns = ['*'], $retrievalMethod = 'all')
    {
        $query = $this->training->with($relations);
        if ($status != null) {
            $query->whereIn('status', $status);
        }
        if ($team) {
            $query->where('teamId', $team->id);
        }
        if ($player) {
            $query->whereRelation('players', 'playerId', $player->id);
        }
        if ($startDate != null && $endDate != null){
            $query->whereBetween('date', [$startDate, $endDate]);
        }
        if ($beforeStartDate) {
            $query->where('startDatetime', '<=', Carbon::now());
        }
        if ($beforeEndDate) {
            $query->where('endDatetime', '<=', Carbon::now());
        }
        if ($reminderNotified != null) {
            $query->where('isReminderNotified', $reminderNotified);
        }
        if ($take){
            $query->take($take);
        }
        $query->orderBy($orderBy, $orderDirection);
        if ($retrievalMethod == 'all') {
            return  $query->get($columns);
        }
        elseif ($retrievalMethod == 'single') {
            return $query->first($columns);
        }
        else {
            return $query->count($columns);
        }

    }

    public function getByRelation($relation, $withRelation = [], $status = null, $startDate = null, $endDate = null, $take = null, $orderBy = 'date', $orderDirection = 'asc', $column = ['*'])
    {
        $query = $relation->trainings()->with($withRelation);
        if ($status != null) {
            $query->whereIn('status', $status);
        }
        if ($startDate != null && $endDate != null){
            $query->whereBetween('date', [$startDate, $endDate]);
        }
        if ($take){
            $query->take($take);
        }
        return $query->orderBy($orderBy, $orderDirection)->get($column);
    }

    public function getAttendanceTrend($startDate = null, $endDate = null,  $teams = null)
    {
        $query = $this->training
            ->join('player_training_attendance', 'trainings.id', '=', 'player_training_attendance.trainingId')
            ->join('players', 'player_training_attendance.playerId', '=', 'players.id');

        if ($teams) {
            $teamIds = collect($teams)->pluck('id')->all();
            $query->join('player_teams', function (JoinClause $join) use ($teamIds) {
                $join->on('players.id', '=', 'player_teams.playerId')
                    ->whereIn('player_teams.teamId', $teamIds);
            });
        }
        $query->select(
            DB::raw('trainings.date as date'),
            DB::raw("COUNT(CASE WHEN player_training_attendance.attendanceStatus = 'Attended' THEN 1 END) AS total_attended_players"),
            DB::raw("COUNT(CASE WHEN player_training_attendance.attendanceStatus = 'Illness' THEN 1 END) AS total_of_ill_players"),
            DB::raw("COUNT(CASE WHEN player_training_attendance.attendanceStatus = 'Injured' THEN 1 END) AS total_of_injured_players"),
            DB::raw("COUNT(CASE WHEN player_training_attendance.attendanceStatus = 'Other' THEN 1 END) AS total_of_other_attendance_status_players")
        );

        $query->where('trainings.status', 'Completed');

        if ($startDate != null && $endDate != null){
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        return $query->groupBy(DB::raw('date'))
            ->orderBy('date')
            ->get();
    }

    public function countAttendanceByStatus($startDate = null, $endDate = null, $teams = null)
    {
        $query = $this->training
            ->join('player_training_attendance', 'trainings.id', '=', 'player_training_attendance.trainingId')
            ->join('players', 'player_training_attendance.playerId', '=', 'players.id');

        if ($teams) {
            $teamIds = collect($teams)->pluck('id')->all();
            $query->join('player_teams', function (JoinClause $join) use ($teamIds) {
                $join->on('players.id', '=', 'player_teams.playerId')
                    ->whereIn('player_teams.teamId', $teamIds);
            });
        }

        $query->select(
                DB::raw('player_training_attendance.attendanceStatus as status'),
                DB::raw('COUNT(player_training_attendance.playerId) AS total_players')
            )
            ->where('player_training_attendance.attendanceStatus', '!=', 'Required Action')
            ->where('trainings.status', 'Completed');

        if ($startDate != null && $endDate != null){
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        return $query->groupBy(DB::raw('player_training_attendance.attendanceStatus'))->get();
    }

    public function playerAttendance(Player $player, $status = null, $startDate = null, $endDate = null): int
    {
        $query = $player->trainings()->where('status', 'Completed');

        if ($startDate != null && $endDate != null){
            $query->whereBetween('date', [$startDate, $endDate]);
        }
        if ($status) {
            $query->where('attendanceStatus', $status);
        }
        return $query->count();
    }

    public function getRelationData(Training $training, $relation, $with = null, $attendanceStatus = null, $teamId = null, $playerId = null, $exceptPlayerId= null, $retrieveType = 'single')
    {
        $query = $training->$relation();
        if ($with != null) {
            $query->with($with);
        }
        if ($attendanceStatus != null) {
            $query->where('attendanceStatus', $attendanceStatus);
        }
        if ($teamId != null) {
            $query->where('teamId', $teamId);
        }
        if ($playerId != null) {
            $query->where('playerId', $playerId);
        }
        if ($exceptPlayerId != null) {
            $query->where('players.id', '!=', $exceptPlayerId);
        }

        if ($retrieveType == 'single') {
            return $query->first();
        } elseif ($retrieveType == 'multiple') {
            return $query->get();
        } else {
            return $query->count();
        }
    }

    public function create(array $data)
    {
        return $this->training->create($data);
    }
}
