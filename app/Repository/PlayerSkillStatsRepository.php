<?php

namespace App\Repository;

use App\Models\MatchModel;
use App\Models\Player;
use App\Models\PlayerSkillStats;
use App\Models\Training;
use Illuminate\Database\Eloquent\Collection;

class PlayerSkillStatsRepository
{
    protected PlayerSkillStats $playerSkillStats;
    public function __construct(PlayerSkillStats $playerSkillStats)
    {
        $this->playerSkillStats = $playerSkillStats;
    }

    public function getByPlayer(Player $player, MatchModel $match = null, Training $training = null, $retrievalMethod = 'all')
    {
        $query = $player->playerSkillStats();
        if ($match){
            $query->where('matchId', $match->id);
        }
        if ($training){
            $query->where('trainingId', $training->id);
        }
        if ($retrievalMethod == 'all'){
            return $query->get();
        } else {
            return $query->first();
        }
    }
}
