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
        return view('pages.admins.academies.leaderboards.index');
    }

    public function playerLeaderboard(){
        return $this->leaderboardService->playerLeaderboard();
    }

    public function teamLeaderboard(){
        return $this->leaderboardService->teamLeaderboard();
    }
}
