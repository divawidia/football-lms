<?php

namespace App\Repository;

use App\Models\MatchModel;
use App\Models\Player;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class EventScheduleRepository
{
    protected MatchModel $match;
    public function __construct(MatchModel $match)
    {
        $this->eventSchedule = $match;
    }

    public function getAll()
    {
        return $this->eventSchedule->all();
    }

    public function getEvent($status, $eventType = null, $take = null, $startDate = null, $endDate = null, $teams = null)
    {
        $query = $this->eventSchedule->with('teams', 'competition', 'players')
            ->whereIn('status', $status)
            ->where('isOpponentTeamMatch', '0');
        if ($eventType) {
            $query->where('eventType', $eventType);
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
        if ($take){
            $query->take($take);
        }
        return $query->orderBy('date', 'desc')->get();
    }

    public function getUpcomingEvent($eventType, $hour)
    {
        return $this->eventSchedule->with('teams', 'competition')
            ->where('eventType', $eventType)
            ->where('status', 'Scheduled')
            ->where('isReminderNotified', '=','0')
            ->whereBetween('startDateTime', [Carbon::now(), Carbon::now()->addHours($hour)])
            ->orderBy('startDateTime')->get();
    }
    public function getScheduledEvent($eventType)
    {
        return $this->eventSchedule->with('teams', 'competition')
            ->where('eventType', $eventType)
            ->where('status', 'Scheduled')
            ->where('startDatetime', '<=', Carbon::now())
            ->get();
    }
    public function getEndingEvent($eventType)
    {
        return $this->eventSchedule->with('teams', 'competition')
            ->where('eventType', $eventType)
            ->where('status', 'Ongoing')
            ->where('endDatetime', '<=', Carbon::now())
            ->get();
    }

    public function getEventByModel($model, $eventType, $status, $take = null, $sortDateDirection = 'asc')
    {
        $query = $model->schedules()->with('teams', 'competition')
            ->where('eventType', $eventType)
            ->whereIn('status', $status);
        if ($take){
            $query->take($take);
        }
        return $query->orderBy('date', $sortDateDirection)->get();
    }

    public function playerLatestEvent(Player $player, $eventType, $take = 2)
    {
        return $player->schedules()
            ->where('eventType', $eventType)
            ->where('status', 'Completed')
            ->take($take)
            ->orderBy('date', 'desc')
            ->get();
    }

    public function getTeamsMatchPlayed(Team $team = null, $teamSide = 'Academy Team', $startDate = null, $endDate = null)
    {
        $query = $this->eventSchedule->whereHas('teams', function($q) use ($team, $teamSide) {
                $q->where('teamSide', $teamSide);
                if ($team != null){
                    $q->where('teamId', $team->id);
                }
            })
            ->where('status', 'Completed')
            ->where('isOpponentTeamMatch', '0')
            ->where('eventType', 'Match');

        if ($startDate != null && $endDate != null){
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return $query->count();
    }

    public function getTeamsEvents(Team $team, $eventType, $status, $latest = false, $take = null)
    {
        $query = $team->schedules()->with('teams', 'competition')
            ->where('eventType', $eventType)
            ->where('status', $status);
        if ($latest){
            $query->latest('date');
        }
        if ($take != null){
            $query->take(2);
        }

        return $query->get();
    }

    public function getAttendanceTrend($startDate, $endDate,  $teams = null, $eventType = null)
    {
        $query = $this->eventSchedule->from('event_schedules AS es')
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
        $query = $this->eventSchedule->from('event_schedules AS es')
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

    public function playerAttendance(Player $player, $status, $startDate, $endDate, $eventType = null) {
        $query = $player->schedules()
            ->where('isOpponentTeamMatch', '0')
            ->where('status', 'Completed');

        if ($startDate != null && $endDate != null){
            $query->whereBetween('date', [$startDate, $endDate]);
        }
        if ($status) {
            $query->where('attendanceStatus', $status);
        }
        if ($eventType) {
            $query->where('eventType', $eventType);
        }
        return $query->count();
    }

    public function find($id)
    {
        return $this->eventSchedule->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->eventSchedule->create($data);
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
