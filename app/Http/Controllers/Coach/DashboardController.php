<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Repository\CoachMatchStatsRepository;
use App\Repository\MatchRepository;
use App\Services\Coach\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private DashboardService $dashboardService;

    public function __construct(CoachMatchStatsRepository $coachMatchStatsRepository, MatchRepository $eventScheduleRepository){
        $this->coachMatchStatsRepository = $coachMatchStatsRepository;
        $this->eventScheduleRepository = $eventScheduleRepository;
        $this->middleware(function ($request, $next) use ($coachMatchStatsRepository, $eventScheduleRepository) {
            $this->dashboardService = new DashboardService($this->getLoggedCoachUser(), $coachMatchStatsRepository, $eventScheduleRepository);
            return $next($request);
        });
    }
    public function index()
    {
        $dataOverview = $this->dashboardService->overviewStats();
        $latestMatches = $this->dashboardService->latestMatch();
        $upcomingMatches = $this->dashboardService->upcomingMatch();
        $upcomingTrainings = $this->dashboardService->upcomingTraining();

        return view('pages.dashboards.coach', [
            'dataOverview' => $dataOverview,
            'latestMatches' => $latestMatches,
            'upcomingMatches' => $upcomingMatches,
            'upcomingTrainings' => $upcomingTrainings
        ]);
    }
}
