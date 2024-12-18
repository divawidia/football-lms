<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\EventSchedule;
use App\Models\Player;
use App\Models\Team;
use App\Models\TrainingVideo;
use App\Repository\Interface\PlayerRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PlayerRepository implements PlayerRepositoryInterface
{
    protected Player $player;
    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    public function getAll()
    {
        return $this->player->with('user', 'teams')->get();
    }

    public function getPLayersByTeams($teams)
    {
        return $this->player->with('user', 'teams', 'position', 'playerSkillStats')->withTeams($teams)->get();
    }

    public function getInArray($playerIds)
    {
        return $this->player->whereIn('id', $playerIds)->get();
    }

    public function getPlayerNotJoinSpecificTeam(Team $team)
    {
        return $this->player->with('user')
            ->whereDoesntHave('teams', function (Builder $query) use ($team){
                $query->where('teamId', $team->id);
            })
            ->get();
    }

    public function getPlayerNotAssignedTrainingCourse(TrainingVideo $trainingVideo)
    {
        return $this->player->with('user')
            ->whereDoesntHave('trainingVideos', function (Builder $query) use ($trainingVideo){
                $query->where('trainingVideoId', $trainingVideo->id);
            })->get();
    }

    public function getAttendedPLayer($startDate, $endDate, $teams = null, $eventType = null, $mostAttended = true, $mostDidntAttend = false)
    {
        $query = $this->player->with('user', 'position');

        if ($teams) {
            $query->withTeams($teams);
        }
        if ($eventType != null) {
            $query->whereRelation('schedules', 'eventType', $eventType);
        }
        if ($mostAttended) {
            $query->withCount([
                'schedules as attended_count' => function ($q) use ($startDate, $endDate){
                    $q->whereBetween('date', [$startDate, $endDate])
                        ->where('status', 'Completed')
                        ->where('attendanceStatus', 'Attended');
                },
                'schedules as schedules_count' => function ($q) use ($startDate, $endDate){
                    $q->whereBetween('date', [$startDate, $endDate])->where('status', 'Completed');
                }
            ])->orderByDesc('attended_count');
        } elseif ($mostDidntAttend) {
            $query->withCount([
                'schedules as didnt_attended_count' => function ($q) use ($startDate, $endDate){
                    $q->whereBetween('date', [$startDate, $endDate])
                        ->where('status', 'Completed')
                        ->where('attendanceStatus', '!=','Attended');
                },
                'schedules as schedules_count' => function ($q) use ($startDate, $endDate){
                    $q->whereBetween('date', [$startDate, $endDate])->where('status', 'Completed');
                }
            ])->orderByDesc('didnt_attended_count');
        }

        return $query->first();
    }

    public function find($id)
    {
        return $this->player->findOrFail($id);
    }

    public function playerAttendanceCount(Player $player, $status = 'Attended', $startDate = null, $endDate = null)
    {
        $query = $player->schedules()->where('attendanceStatus', $status);

        // If date range is provided, add a whereBetween clause
        if ($startDate && $endDate) {
            $query->whereBetween('player_attendance.updated_at', [$startDate, $endDate]);
        }
        $query->where('status', 'Completed');
        return $query->count();
    }

    public function playerMatchStatsSum(Player $player, $stats, $startDate = null, $endDate = null)
    {
        $query = $player->playerMatchStats();

        // If date range is provided, add a whereBetween clause
        if ($startDate && $endDate) {
            $query->whereBetween('player_match_stats.updated_at', [$startDate, $endDate]);
        }
        return $query->sum($stats);
    }

    public function countMatchPlayed(Player $player, $startDate = null, $endDate = null)
    {
        $query = $player->playerMatchStats()->where('minutesPlayed', '>', 0);
        if ($startDate && $endDate) {
            $query->whereBetween('player_match_stats.updated_at', [$startDate, $endDate]);
        }
        return $query->count();
    }

    public function matchResults(Player $player, $result, $startDate = null, $endDate = null)
    {
        $query = $player->schedules()
            ->where('eventType', 'Match')
            ->whereHas('teams', function ($q) use ($result){
                $q->where('resultStatus', $result);
            });
        if ($startDate && $endDate) {
            $query->whereBetween('event_schedules.date', [$startDate, $endDate]);
        }
        return $query->count();
    }

    public function create(array $data)
    {
        $coach = $this->player->create($data);
        if (array_key_exists('team',$data)){
            $coach->teams()->attach($data['team']);
        }
        return $coach;
    }

    public function update(Player $player, array $data)
    {
        $player->update($data);
        $player->user->update($data);
        return $player;
    }

    public function delete($id)
    {
        $post = $this->find($id);
        $post->delete();
        return $post;
    }
}
