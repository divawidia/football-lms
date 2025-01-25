<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
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

        return view('pages.dashboards.player', [
            'data' => $player,
            'teams'=> $player->teams,
            'playerUpcomingMatches' => $this->playerService->playerUpcomingMatches($player),
            'playerUpcomingTrainings'=> $this->playerService->playerUpcomingTrainings($player),
            'playerMatchPlayed' => $this->playerService->playerMatchPlayed($player),
            'playerMatchPlayedThisMonth' => $this->playerService->playerMatchPlayedThisMonth($player),
            'playerStats'=>$this->playerService->playerStats($player),
            'matchResults' => $this->playerService->matchStats($player),
            'winRate' =>$this->playerService->winRate($player),
            'performanceReviews' => $player->playerPerformanceReview,
            'playerSkillStats' => $this->playerService->skillStatsChart($player),
            'latestMatches' => $this->playerService->latestMatches($player),
            'latestTrainings' => $this->playerService->latestTrainings($player),
        ]);
    }
}
