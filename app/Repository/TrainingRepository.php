<?php

namespace App\Repository;

use App\Models\Player;
use App\Models\Training;
use App\Repository\Interface\TrainingRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TrainingRepository implements TrainingRepositoryInterface
{
    protected Training $training;
    public function __construct(Training $training)
    {
        $this->training = $training;
    }

    public function getAll($relations = ['team'], Collection $teams = null, Player $player = null, $status = null, $take = null, $startDate = null, $endDate = null, $beforeStartDate = false, $beforeEndDate = false, $reminderNotified = null, $orderBy = 'date', $orderDirection = 'desc', $columns = ['*'], $retrievalMethod = 'all')
    {
        $query = $this->training->with($relations);
        if ($status != null) {
            $query->whereIn('status', $status);
        }
        if ($teams != null) {
            $teamIds = collect($teams)->pluck('id')->all();
            $query->whereIn('teamId', $teamIds);
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

    public function getAttendanceTrend(string $startDate = null, string $endDate = null, Collection $teams = null)
    {
        $query = $this->training->join('player_training_attendance', 'trainings.id', '=', 'player_training_attendance.trainingId');

        if ($teams) {
            $teamIds = collect($teams)->pluck('id')->all();
            $query->whereIn('player_training_attendance.teamId', $teamIds);
        }

        $query->select(
            DB::raw('trainings.date as date'),
            DB::raw("COUNT(CASE WHEN player_training_attendance.attendanceStatus = 'Attended' THEN 1 END) AS total_attended_players"),
            DB::raw("COUNT(CASE WHEN player_training_attendance.attendanceStatus = 'Illness' THEN 1 END) AS total_of_ill_players"),
            DB::raw("COUNT(CASE WHEN player_training_attendance.attendanceStatus = 'Injured' THEN 1 END) AS total_of_injured_players"),
            DB::raw("COUNT(CASE WHEN player_training_attendance.attendanceStatus = 'Other' THEN 1 END) AS total_of_other_status_players"),
            DB::raw("COUNT(CASE WHEN player_training_attendance.attendanceStatus = 'Required Action' THEN 1 END) AS total_of_required_action_status_players")
        )->where('trainings.status', 'Completed');

        if ($startDate != null && $endDate != null){
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        return $query->groupBy(DB::raw('date'))->orderBy('date')->get();
    }

    public function countAttendanceByStatus(string $startDate = null, string $endDate = null, Collection $teams = null)
    {
        $query = $this->training->join('player_training_attendance', 'trainings.id', '=', 'player_training_attendance.trainingId');

        if ($teams) {
            $teamIds = collect($teams)->pluck('id')->all();
            $query->whereIn('player_training_attendance.teamId', $teamIds);
        }

        $query->select(DB::raw('player_training_attendance.attendanceStatus as status'), DB::raw('COUNT(player_training_attendance.playerId) AS total_players'))
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
        $training = $this->training->create($data);
        $training->players()->attach($training->team->players, ['teamId' => $training->teamId]);
        $training->coaches()->attach($training->team->coaches, ['teamId' => $training->teamId]);
        return $training;
    }

    public function find($id)
    {
        return $this->training->findOrFail($id);
    }
}
