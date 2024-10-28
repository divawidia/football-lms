<?php

namespace App\Repository;

use App\Models\EventSchedule;
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

    public function getByPlayer(Player $player, EventSchedule $schedule = null)
    {
        $query = $player->playerPerformanceReview();
        if ($schedule){
            $query->where('eventId', $schedule->id);
        }
        return $query->get();
    }

    public function create(array $data)
    {
        return $this->playerPerformanceReview->create($data);
    }
}
