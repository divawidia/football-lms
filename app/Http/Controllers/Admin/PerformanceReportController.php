<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PerformanceReportService;
use Illuminate\Http\Request;

class PerformanceReportController extends Controller
{
    private PerformanceReportService $performanceReportService;

    public function __construct(PerformanceReportService $performanceReportService)
    {
        $this->performanceReportService = $performanceReportService;
    }
    public function index(){
        if (\request()->ajax()){
            return $this->performanceReportService->matchHistory();
        }
        $latestMatches = $this->performanceReportService->latestMatch();
        $overviewStats = $this->performanceReportService->overviewStats();
//        dd($overviewStats);

        return view('pages.admins.academies.reports.performance.index', [
            'latestMatches' => $latestMatches,
            'overviewStats' => $overviewStats
        ]);
    }
}
