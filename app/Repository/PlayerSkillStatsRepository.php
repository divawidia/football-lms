<?php

namespace App\Repository;

use App\Models\MatchModel;
use App\Models\Player;
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

    public function getByPlayer(Player $player, MatchModel $match = null)
    {
        $query = $player->playerSkillStats();
        if ($match){
            $query->where('eventId', $match->id);
        }
        return $query->get();
    }

    public function create(array $data)
    {
        return $this->playerSkillStats->create($data);
    }
}
