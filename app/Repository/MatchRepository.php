<?php

namespace App\Repository;

use App\Models\MatchModel;
use App\Models\Player;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class MatchRepository
{
    protected MatchModel $match;
    public function __construct(MatchModel $match)
    {
        $this->match = $match;
    }

    public function getAll($relations = ['teams', 'competition', 'players'], $teams = null, $status = null, $take = null, $startDate = null, $endDate = null, $beforeStartDate = false, $beforeEndDate = false, $reminderNotified = null, $orderBy = 'date', $orderDirection = 'desc', $columns = ['*']): Collection|array
    {
        $query = $this->match->with($relations);
        if ($status != null) {
            $query->whereIn('status', $status);
        }
        if ($teams != null) {
            $teamIds = collect($teams)->pluck('id')->all();
            $query->whereHas('teams', function (Builder $q) use ($teamIds) {
                $q->whereIn('teamId', $teamIds);
            });
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
        return $query->orderBy($orderBy, $orderDirection)->get($columns);
    }

    public function getByRelation($relation, $withRelation = [], $status = null, $startDate = null, $endDate = null, $take = null, $orderBy = 'date', $orderDirection = 'asc', $column = ['*'])
    {
        $query = $relation->matches()->with($withRelation);
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

    public function getEventByModel($model, $status = [], $take = null, $orderBy = 'date', $orderDirection = 'asc', $column = ['*'])
    {
        $query = $model->matches()->with('teams', 'competition')
            ->whereIn('status', $status);
        if ($take){
            $query->take($take);
        }
        return $query->orderBy($orderBy, $orderDirection)->get($column);
    }

    public function getAttendanceTrend($startDate, $endDate,  $teams = null, $eventType = null)
    {
        $query = $this->match->from('event_schedules AS es')
            ->join('player_attendance as pa', 'es.id', '=', 'pa.scheduleId')
            ->join('players as p', 'pa.playerId', '=', 'p.id');

        if ($teams != null) {
            $teamIds = collect($teams)->pluck('id')->all();
            $query->join('player_teams', function (JoinClause $join) use ($teamIds) {
                $join->on('p.id', '=', 'player_teams.playerId')
                    ->whereIn('player_teams.teamId', $teamIds);
            });
        }
        $query->select(
            DB::raw('es.date as date'),
            DB::raw("COUNT(CASE WHEN pa.attendanceStatus = 'Attended' THEN 1 END) AS total_attended_players"),
            DB::raw("COUNT(CASE WHEN pa.attendanceStatus = 'Illness' THEN 1 END) AS total_of_ill_players"),
            DB::raw("COUNT(CASE WHEN pa.attendanceStatus = 'Injured' THEN 1 END) AS total_of_injured_players"),
            DB::raw("COUNT(CASE WHEN pa.attendanceStatus = 'Other' THEN 1 END) AS total_of_other_attendance_status_players")
        );

        $query->where('es.status', 'Completed');

        if ($eventType != null) {
            $query->where('es.eventType', $eventType);
        }
//        if ($player) {
//            $query->where('p.id', $player->id);
//        }

        return $query->whereBetween('date', [$startDate, $endDate])
            ->groupBy(DB::raw('date'))
            ->orderBy('date')
            ->get();
    }

    public function countAttendanceByStatus($startDate, $endDate, $teams = null, $eventType = null)
    {
        $query = $this->match->from('event_schedules AS es')
            ->join('player_attendance as pa', 'es.id', '=', 'pa.scheduleId')
            ->join('players as p', 'pa.playerId', '=', 'p.id');

        if ($teams != null){
            $teamIds = collect($teams)->pluck('id')->all();
            $query->join('player_teams', function (JoinClause $join) use ($teamIds) {
                $join->on('p.id', '=', 'player_teams.playerId')
                    ->whereIn('player_teams.teamId', $teamIds);
            });
        }

        $query->select(DB::raw('pa.attendanceStatus as status'), DB::raw('COUNT(pa.playerId) AS total_players'))
            ->where('pa.attendanceStatus', '!=', 'Required Action')
            ->where('es.status', 'Completed');

        if ($eventType != null) {
            $query->where('es.eventType', $eventType);
        }
//        if ($player) {
//            $query->where('p.id', $player->id);
//        }

        return $query->whereBetween('date', [$startDate, $endDate])
            ->groupBy(DB::raw('pa.attendanceStatus'))
            ->get();
    }

    public function getRelationData(MatchModel $match, $relation, $with = null, $attendanceStatus = null, $teamId = null, $playerId = null, $exceptPlayerId= null, $retrieveType = 'single')
    {
        $query = $match->$relation();
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

    public function playerAttendance(Player $player, $status = null, $startDate = null, $endDate = null): int
    {
        $query = $player->matches()->where('status', 'Completed');

        if ($startDate != null && $endDate != null){
            $query->whereBetween('date', [$startDate, $endDate]);
        }
        if ($status) {
            $query->where('attendanceStatus', $status);
        }
        return $query->count();
    }

    public function find($id)
    {
        return $this->match->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->match->create($data);
    }
    public function createRelation(MatchModel $match, array $data, $relation)
    {
        return $match->$relation()->create($data);
    }

    public function update($id, array $data)
    {
        $post = $this->find($id);
        $post->update($data);
        return $post;
    }

    public function updateStatus(MatchModel $match, $status)
    {
        return $match->update(['status' => $status]);
    }

    public function updateTeamMatchStats(MatchModel $match, array $data)
    {
        return $match->teams()->updateExistingPivot($data['teamId'], [
            "teamPossesion" => $data['teamPossesion'],
            "teamShotOnTarget" => $data['teamShotOnTarget'],
            "teamShots" => $data['teamShots'],
            "teamTouches" => $data['teamTouches'],
            "teamTackles" => $data['teamTackles'],
            "teamClearances" => $data['teamClearances'],
            "teamCorners" => $data['teamCorners'],
            "teamOffsides" => $data['teamOffsides'],
            "teamYellowCards" => $data['teamYellowCards'],
            "teamRedCards" => $data['teamRedCards'],
            "teamFoulsConceded" => $data['teamFoulsConceded'],
            "teamPasses" => $data['teamPasses'],
        ]);
    }

    public function updateExternalTeamMatchStats(MatchModel $match, array $data)
    {
        return $match->externalTeam->update($data);
    }

    public function delete($id)
    {
        $post = $this->find($id);
        $post->delete();
        return $post;
    }
}
