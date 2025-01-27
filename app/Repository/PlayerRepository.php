<?php

namespace App\Repository;

use App\Models\Player;
use App\Models\Team;
use App\Models\TrainingVideo;
use App\Repository\Interface\PlayerRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class PlayerRepository implements PlayerRepositoryInterface
{
    protected Player $player;
    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    public function getAll($teams = null, $position = null, $skill = null, $status = null)
    {
        $query = $this->player->with('user', 'teams', 'position', 'playerSkillStats');
        if ($teams) {
            $query->withTeams($teams);
        }
        if ($position) {
            $query->where('positionId', $position);
        }
        if ($skill) {
            $query->where('skill', $skill);
        }
        if ($status != null) {
            $query->whereRelation('user','status', $status);
        }
        return $query->get();
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
    public function getMostAttended($startDate = null, $endDate = null, $teams = null, $relation = 'trainings')
    {
        $query = $this->player->with('user', 'position');

        if ($teams) {
            $query->withTeams($teams);
        }
        $query->withCount([
            $relation.' as attended_count' => function ($q) use ($startDate, $endDate){
                if ($startDate != null && $endDate != null) {
                    $q->whereBetween('date', [$startDate, $endDate]);
                }
                $q->where('status', 'Completed')->where('attendanceStatus', 'Attended');
            },
            $relation.' as schedules_count' => function ($q) use ($startDate, $endDate){
                if ($startDate != null && $endDate != null) {
                    $q->whereBetween('date', [$startDate, $endDate]);
                }
                $q->where('status', 'Completed');
            }
        ])->orderByDesc('attended_count');
        return $query->first();
    }
    public function getMostDidntAttended($startDate = null, $endDate = null, $teams = null, $relation = 'trainings')
    {
        $query = $this->player->with('user', 'position');

        if ($teams) {
            $query->withTeams($teams);
        }
        $query->withCount([
            $relation.' as didnt_attended_count' => function ($q) use ($startDate, $endDate){
                if ($startDate != null && $endDate != null) {
                    $q->whereBetween('date', [$startDate, $endDate]);
                }
                $q->where('status', 'Completed')->where('attendanceStatus', '!=','Attended');
            },
            $relation.' as schedules_count' => function ($q) use ($startDate, $endDate){
                if ($startDate != null && $endDate != null) {
                    $q->whereBetween('date', [$startDate, $endDate]);
                }
                $q->where('status', 'Completed');
            }
        ])->orderByDesc('didnt_attended_count');
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

    public function playerMatchStatsSum(Player $player, $stats, $startDate = null, $endDate = null, Team $team = null)
    {
        $query = $player->playerMatchStats();

        // If date range is provided, add a whereBetween clause
        if ($startDate && $endDate) {
            $query->whereBetween('player_match_stats.updated_at', [$startDate, $endDate]);
        }
        if ($team) {
            $query->where('teamId', $team->id);
        }
        return $query->sum($stats);
    }

    public function countMatchPlayed(Player $player, $startDate = null, $endDate = null, Team $team = null)
    {
        $query = $player->playerMatchStats()->where('minutesPlayed', '>', 0);
        if ($startDate && $endDate) {
            $query->whereBetween('player_match_stats.updated_at', [$startDate, $endDate]);
        }
        if ($team) {
            $query->where('teamId', $team->id);
        }
        return $query->count();
    }

    public function matchResults(Player $player, $result = null, $startDate = null, $endDate = null)
    {
        $query = $player->matches();
        if ($result) {
            $query->whereHas('teams', function ($q) use ($result){
                $q->where('resultStatus', $result);
            });
        }

        if ($startDate && $endDate) {
            $query->whereBetween('matches.date', [$startDate, $endDate]);
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
