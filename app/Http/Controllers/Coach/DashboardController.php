<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Repository\CoachMatchStatsRepository;
use App\Repository\MatchRepository;
use App\Services\Coach\DashboardService;
use App\Services\CoachService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private CoachService $coachService;

    public function __construct(CoachService $coachService){
        $this->coachService = $coachService;
    }
    public function index()
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now();
        $coach = $this->getLoggedCoachUser();

        return view('pages.dashboards.coach', [
            'teams' => $coach->teams,
            'matchPlayed' => $this->coachService->totalMatchPlayed($coach),
            'matchPlayedThisMonth' => $this->coachService->totalMatchPlayed($coach, $startDate, $endDate),
            'goals' => $this->coachService->totalGoals($coach),
            'goalsThisMonth' => $this->coachService->totalGoals($coach, $startDate, $endDate),
            'goalConceded' => $this->coachService->goalConceded($coach),
            'goalConcededThisMonth' => $this->coachService->goalConceded($coach, $startDate, $endDate),
            'winRate' => $this->coachService->winRate($coach),
            'winRateThisMonth' => $this->coachService->winRate($coach, $startDate, $endDate),
            'wins' => $this->coachService->wins($coach),
            'winsThisMonth' => $this->coachService->wins($coach, $startDate, $endDate),
            'lose' => $this->coachService->lose($coach),
            'loseThisMonth' => $this->coachService->lose($coach, $startDate, $endDate),
            'draw' => $this->coachService->draw($coach),
            'drawThisMonth' => $this->coachService->draw($coach, $startDate, $endDate),
            'goalsDifference' => $this->coachService->goalsDifference($coach),
            'goalsDifferenceThisMonth' => $this->coachService->goalsDifference($coach, $startDate, $endDate),
            'cleanSheets' => $this->coachService->goalsDifference($coach),
            'cleanSheetsThisMonth' => $this->coachService->goalsDifference($coach, $startDate, $endDate),
            'latestMatches' => $this->coachService->latestMatches($coach),
            'upcomingMatches' => $this->coachService->upcomingMatches($coach),
            'upcomingTrainings' => $this->coachService->upcomingTrainings($coach)
        ]);
    }
}
