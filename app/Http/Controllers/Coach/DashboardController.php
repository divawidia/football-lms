<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Services\Coach\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private DashboardService $dashboardService;

    public function __construct(){
        $this->middleware(function ($request, $next) {
            $this->dashboardService = new DashboardService($this->getLoggedCoachUser());
            return $next($request);
        });
    }
    public function index()
    {
        $dataOverview = $this->dashboardService->overviewStats();
        $latestMatches = $this->dashboardService->latestMatch();
        $upcomingMatches = $this->dashboardService->upcomingMatch();
        $upcomingTrainings = $this->dashboardService->upcomingTraining();

        return view('pages.coaches.dashboard', [
            'dataOverview' => $dataOverview,
            'latestMatches' => $latestMatches,
            'upcomingMatches' => $upcomingMatches,
            'upcomingTrainings' => $upcomingTrainings
        ]);
    }
}
