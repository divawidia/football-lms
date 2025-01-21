<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminService;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private DashboardService $dashboardService;
    private AdminService $adminService;

    public function __construct(
        DashboardService $dashboardService,
        AdminService $adminService,
    )
    {
        $this->dashboardService = $dashboardService;
        $this->adminService = $adminService;
    }
    public function index()
    {
        $dataOverview = $this->dashboardService->overviewStats();
        $teamAgeChart = $this->dashboardService->playerAgeChart();
        $upcomingMatches = $this->dashboardService->upcomingMatches();
        $upcomingTrainings = $this->dashboardService->upcomingTrainings();

        return view('pages.dashboards.admin', [
            'totalAdmins' => $this->adminService->countAllAdmin(),
            'totalAdminsThisMonth' => $this->adminService->countNewAdminThisMonth(),
            'dataOverview' => $dataOverview,
            'teamAgeChart' => $teamAgeChart,
            'upcomingMatches' => $upcomingMatches,
            'upcomingTrainings' => $upcomingTrainings
        ]);
    }
}
