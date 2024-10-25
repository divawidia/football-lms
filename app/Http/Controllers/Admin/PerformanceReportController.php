<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CompetitionService;
use App\Services\PerformanceReportService;
use Illuminate\Http\Request;

class PerformanceReportController extends Controller
{
    private PerformanceReportService $performanceReportService;
    private CompetitionService $competitionService;

    public function __construct(PerformanceReportService $performanceReportService, CompetitionService $competitionService)
    {
        $this->performanceReportService = $performanceReportService;
        $this->competitionService = $competitionService;
    }
    public function index(){
        if (\request()->ajax()){
            return $this->performanceReportService->matchHistory();
        }
        $latestMatches = $this->performanceReportService->latestMatch();
        $overviewStats = $this->performanceReportService->overviewStats();
        $competitions = $this->competitionService->index();

        return view('pages.admins.academies.reports.performance.index', [
            'latestMatches' => $latestMatches,
            'overviewStats' => $overviewStats,
            'competitions' => $competitions
        ]);
    }

    public function coachIndex(){
        $coach = $this->getLoggedCoachUser();
        if (\request()->ajax()){
            return $this->performanceReportService->matchHistory();
        }
        $latestMatches = $this->performanceReportService->latestMatch();
        $overviewStats = $this->performanceReportService->coachOverviewStats($coach);
        $competitions = $this->competitionService->index();

        return view('pages.admins.academies.reports.performance.index', [
            'latestMatches' => $latestMatches,
            'overviewStats' => $overviewStats,
            'competitions' => $competitions
        ]);
    }

}
