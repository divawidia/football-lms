<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService){
        $this->dashboardService = $dashboardService;
    }
    public function index()
    {
        $dataOverview = $this->dashboardService->overviewStats();
        return view('pages.admins.dashboard', [
            'dataOverview' => $dataOverview,
        ]);
    }
}
