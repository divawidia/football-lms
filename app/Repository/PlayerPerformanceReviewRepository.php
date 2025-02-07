<?php

namespace App\Repository;

use App\Models\MatchModel;
use App\Models\Player;
use App\Models\PlayerPerformanceReview;
use App\Models\Training;

class PlayerPerformanceReviewRepository
{
    public function getByPlayer(Player $player, MatchModel $match = null, Training $training = null, $retrievalMethod = 'all')
    {
        $query = $player->playerPerformanceReview();
        if ($match){
            $query->where('matchId', $match->id);
        }
        if ($training){
            $query->where('trainingId', $training->id);
        }

        if ($retrievalMethod == 'all'){
            return $query->latest()->get();
        } else {
            return $query->first();
        }
    }
}
