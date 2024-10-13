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

    public function __construct(DashboardService $service){
        $this->dashboardService = $service;
    }
    public function index()
    {
        $coachId = Coach::where('userId', $this->getLoggedUserId())->select('id')->first();
        $dataOverview = $this->dashboardService->overviewStats($coachId);
        $latestMatches = $this->dashboardService->latestMatch($coachId);
        $upcomingMatches = $this->dashboardService->upcomingMatch($coachId);
        $upcomingTrainings = $this->dashboardService->upcomingTraining($coachId);

        return view('pages.coaches.dashboard', [
            'dataOverview' => $dataOverview,
            'latestMatches' => $latestMatches,
            'upcomingMatches' => $upcomingMatches,
            'upcomingTrainings' => $upcomingTrainings
        ]);
    }
}
