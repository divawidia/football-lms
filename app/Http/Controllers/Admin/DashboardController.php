<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService){
        $this->dashboardService = $dashboardService;
    }
    public function index()
    {
        $dataOverview = $this->dashboardService->overviewStats();
//        $revenueChart = $this->dashboardService->revenueChart();
        $teamAgeChart = $this->dashboardService->playerAgeChart();
        $upcomingMatches = $this->dashboardService->upcomingMatches();
        $upcomingTrainings = $this->dashboardService->upcomingTrainings();

        return view('pages.admins.dashboard', [
            'dataOverview' => $dataOverview,
//            'revenueChart' => $revenueChart,
            'teamAgeChart' => $teamAgeChart,
            'upcomingMatches' => $upcomingMatches,
            'upcomingTrainings' => $upcomingTrainings
        ]);
    }
}
