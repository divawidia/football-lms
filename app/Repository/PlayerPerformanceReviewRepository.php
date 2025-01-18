<?php

namespace App\Repository;

use App\Models\MatchModel;
use App\Models\Player;
use App\Models\PlayerPerformanceReview;

class PlayerPerformanceReviewRepository
{
    protected PlayerPerformanceReview $playerPerformanceReview;
    public function __construct(PlayerPerformanceReview $playerPerformanceReview)
    {
        $this->playerPerformanceReview = $playerPerformanceReview;
    }

    public function getAll()
    {
        return $this->playerPerformanceReview->all();
    }

    public function getByPlayer(Player $player, MatchModel $match = null)
    {
        $query = $player->playerPerformanceReview();
        if ($match){
            $query->where('eventId', $match->id);
        }
        return $query->get();
    }

    public function create(array $data)
    {
        return $this->playerPerformanceReview->create($data);
    }
}
