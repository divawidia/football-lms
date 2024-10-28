<?php

namespace App\Services;

use App\Models\Coach;
use App\Models\EventSchedule;
use App\Models\Player;
use App\Models\PlayerPerformanceReview;
use App\Models\PlayerSkillStats;
use App\Repository\PlayerPerformanceReviewRepository;

class PlayerPerformanceReviewService extends Service
{
    private PlayerPerformanceReviewRepository $performanceReviewRepository;
    public function __construct(PlayerPerformanceReviewRepository $performanceReviewRepository)
    {
        $this->performanceReviewRepository = $performanceReviewRepository;
    }

    public function index(Player $player)
    {
        return $this->performanceReviewRepository->getByPlayer($player);
    }

    public function getByEvent(Player $player, EventSchedule $schedule)
    {
        return $this->performanceReviewRepository->getByPlayer($player, $schedule);
    }

    public function store(array $data, Player $player, Coach $coach)
    {
        $data['playerId'] = $player->id;
        $data['coachId'] = $coach->id;
        return $this->performanceReviewRepository->create($data);
    }

    public function update(array $data, PlayerPerformanceReview $review)
    {
        return $review->update($data);
    }

    public function destroy(PlayerPerformanceReview $review)
    {
        return $review->delete();
    }
}
