<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\EventSchedule;
use App\Models\Player;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PlayerRepository
{
    protected Player $player;
    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    public function getAll()
    {
        return $this->player->all();
    }

    public function getCoachsPLayers($teams)
    {
        return $this->player->withTeams($teams)->get();
    }

    public function getMostAttendedPLayer()
    {
        return $this->player->with('schedules', 'user')
            ->withCount(['schedules', 'schedules as attended_count' => function ($query){
                $query->where('attendanceStatus', 'Attended');
            }])
            ->orderBy('attended_count', 'desc')
            ->first();
    }
    public function getMostAttendedCoachsPLayer($teams)
    {
        return $this->player->with('schedules', 'user')
            ->withTeams($teams)
            ->withCount(['schedules', 'schedules as attended_count' => function ($query){
                $query->where('attendanceStatus', 'Attended');
            }])
            ->orderBy('attended_count', 'desc')
            ->first();
    }

    public function getMostDidntAttendPLayer()
    {
        return $this->player->with('schedules', 'user')
            ->withCount(['schedules', 'schedules as didnt_attended_count' => function ($query){
                $query->where('attendanceStatus', 'Illness')
                    ->orWhere('attendanceStatus', 'Injured')
                    ->orWhere('attendanceStatus', 'Other');
            }])
            ->orderBy('didnt_attended_count', 'desc')
            ->first();
    }

    public function getMostDidntAttendCoachsPLayer($teams)
    {
        return $this->player->with('schedules', 'user')
            ->withTeams($teams)
            ->withCount(['schedules', 'schedules as didnt_attended_count' => function ($query){
                $query->where('attendanceStatus', 'Illness')
                    ->orWhere('attendanceStatus', 'Injured')
                    ->orWhere('attendanceStatus', 'Other');
            }])
            ->orderBy('didnt_attended_count', 'desc')
            ->first();
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
            $query->whereBetween('updated_at', [$startDate, $endDate]);
        }

        return $query->count();
    }

    public function create(array $data)
    {
        return $this->player->create($data);
    }

    public function update($id, array $data)
    {
        $post = $this->find($id);
        $post->update($data);
        return $post;
    }

    public function delete($id)
    {
        $post = $this->find($id);
        $post->delete();
        return $post;
    }
}
