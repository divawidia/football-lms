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
        $coachId = Coach::where('userId', $this->getLoggedUserId())->select('id')->first();
        $this->dashboardService = new DashboardService($coachId);
    }
    public function index()
    {
        $dataOverview = $this->dashboardService->overviewStats();
        $latestMatch = $this->dashboardService->latestMatch();
        $upcomingMatches = $this->dashboardService->upcomingMatch();
        $upcomingTrainings = $this->dashboardService->upcomingTraining();

        return view('pages.admins.dashboard', [
            'dataOverview' => $dataOverview,
            'lastestMatch' => $latestMatch,
            'upcomingMatches' => $upcomingMatches,
            'upcomingTrainings' => $upcomingTrainings
        ]);
    }
}
