<?php

namespace App\Http\Controllers;

use App\Services\CompetitionService;
use App\Services\PerformanceReportService;

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

        return view('pages.academies.reports.performance.index', [
            'latestMatches' => $latestMatches,
            'overviewStats' => $overviewStats,
            'competitions' => $competitions
        ]);
    }

    public function coachIndex(){
        $coach = $this->getLoggedCoachUser();

        if (\request()->ajax()){
            return $this->performanceReportService->coachMatchHistory($coach);
        }
        $latestMatches = $this->performanceReportService->coachLatestMatch($coach);
        $overviewStats = $this->performanceReportService->coachOverviewStats($coach);
        $competitions = $this->competitionService->coachTeamsIndex($coach);

        return view('pages.academies.reports.performance.index', [
            'latestMatches' => $latestMatches,
            'overviewStats' => $overviewStats,
            'competitions' => $competitions
        ]);
    }

}
