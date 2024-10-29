<?php

namespace App\Repository;

use App\Models\EventSchedule;
use App\Models\Player;
use App\Models\PlayerPerformanceReview;
use App\Models\PlayerSkillStats;

class PlayerSkillStatsRepository
{
    protected PlayerSkillStats $playerSkillStats;
    public function __construct(PlayerSkillStats $playerSkillStats)
    {
        $this->playerSkillStats = $playerSkillStats;
    }

    public function getAll()
    {
        return $this->playerSkillStats->all();
    }

    public function getByPlayer(Player $player, EventSchedule $schedule = null)
    {
        $query = $player->playerSkillStats();
        if ($schedule){
            $query->where('eventId', $schedule->id);
        }
        return $query->get();
    }

    public function create(array $data)
    {
        return $this->playerSkillStats->create($data);
    }
}
