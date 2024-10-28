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
        if (isAllAdmin()){
            $latestMatches = $this->performanceReportService->latestMatch();
            $overviewStats = $this->performanceReportService->overviewStats();
            $competitions = $this->competitionService->index();
            $matchHistoryRoutes = url()->route('admin.performance-report.index');
        } elseif (isCoach()){
            $coach = $this->getLoggedCoachUser();
            $latestMatches = $this->performanceReportService->coachLatestMatch($coach);
            $overviewStats = $this->performanceReportService->coachOverviewStats($coach);
            $competitions = $this->competitionService->coachTeamsIndex($coach);
            $matchHistoryRoutes = url()->route('coach.performance-report.index');
        }


        return view('pages.academies.reports.performance.index', [
            'latestMatches' => $latestMatches,
            'overviewStats' => $overviewStats,
            'competitions' => $competitions,
            'matchHistoryRoutes' => $matchHistoryRoutes
        ]);
    }

    public function adminIndex()
    {
        return $this->performanceReportService->matchHistory();
    }

    public function coachIndex(){
        $coach = $this->getLoggedCoachUser();
            return $this->performanceReportService->coachMatchHistory($coach);
    }

}
