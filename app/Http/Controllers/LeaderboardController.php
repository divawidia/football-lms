<?php

namespace App\Http\Controllers;

use App\Services\LeaderboardService;

class LeaderboardController extends Controller
{
    private LeaderboardService $leaderboardService;

    public function __construct(LeaderboardService $leaderboardService){
        $this->leaderboardService = $leaderboardService;
    }
    public function index(){
        if (isAllAdmin()){
            $teamsLeaderboardRoute = route('leaderboards.teams');
            $playersLeaderboardRoute = route('leaderboards.players');
        } elseif (isCoach()){
            $teamsLeaderboardRoute = route('leaderboards.coach-teams');
            $playersLeaderboardRoute = route('leaderboards.coach-players');
        } else {
            $teamsLeaderboardRoute = route('leaderboards.player-teams');
            $playersLeaderboardRoute = route('leaderboards.player-teammate');
        }
        return view('pages.academies.leaderboards.index', [
            'teamsLeaderboardRoute' => $teamsLeaderboardRoute,
            'playersLeaderboardRoute' => $playersLeaderboardRoute,
        ]);
    }

    public function playerLeaderboard(){
        return $this->leaderboardService->playerLeaderboard();
    }
    public function coachPlayerLeaderboard(){
        return $this->leaderboardService->coachPLayerLeaderboard($this->getLoggedCoachUser());
    }
    public function playersTeammateLeaderboard(){
        return $this->leaderboardService->playersTeammateLeaderboard($this->getLoggedPLayerUser());
    }

    public function teamLeaderboard(){
        return $this->leaderboardService->teamLeaderboard();
    }
    public function coachTeamLeaderboard(){
        return $this->leaderboardService->modelsTeamsLeaderboards($this->getLoggedCoachUser());
    }

    public function playerTeamLeaderboard(){
        return $this->leaderboardService->modelsTeamsLeaderboards($this->getLoggedPLayerUser());
    }
}
