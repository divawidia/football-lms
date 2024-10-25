<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LeaderboardService;

class LeaderboardController extends Controller
{
    private LeaderboardService $leaderboardService;

    public function __construct(LeaderboardService $leaderboardService){
        $this->leaderboardService = $leaderboardService;
    }
    public function index(){
        return view('pages.academies.leaderboards.index');
    }

    public function playerLeaderboard(){
        return $this->leaderboardService->playerLeaderboard();
    }
    public function coachPlayerLeaderboard(){
        return $this->leaderboardService->coachPLayerLeaderboard($this->getLoggedCoachUser());
    }

    public function teamLeaderboard(){
        return $this->leaderboardService->teamLeaderboard();
    }
    public function coachTeamLeaderboard(){
        return $this->leaderboardService->coachsTeamLeaderboards($this->getLoggedCoachUser());
    }
}
