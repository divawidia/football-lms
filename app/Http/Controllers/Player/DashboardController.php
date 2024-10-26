<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Services\Coach\DashboardService;
use App\Services\PlayerService;
class DashboardController extends Controller
{
    private PlayerService $playerService;
    public function __construct(PlayerService $playerService){
        $this->playerService = $playerService;
    }
    public function index()
    {
        $player = $this->getLoggedPLayerUser();

        $overview = $this->playerService->show($player);
        $performanceReviews = $player->playerPerformanceReview;
        $teams = $player->teams;
        $playerSkillStats = $this->playerService->skillStatsChart($player);
        $latestMatch = $this->playerService->playerLatestMatch($player);
        $latestTraining = $this->playerService->playerLatestTraining($player);

        return view('pages.players.dashboard', [
            'data' => $player,
            'teams'=> $teams,
            'overview' => $overview,
            'performanceReviews' => $performanceReviews,
            'playerSkillStats' => $playerSkillStats,
            'latestMatch' => $latestMatch,
            'latestTraining' => $latestTraining,
        ]);
    }
}
