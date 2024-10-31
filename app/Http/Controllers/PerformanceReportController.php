<?php

namespace App\Http\Controllers;

use App\Services\CompetitionService;
use App\Services\PerformanceReportService;
use App\Services\PlayerService;

class PerformanceReportController extends Controller
{
    private PerformanceReportService $performanceReportService;
    private CompetitionService $competitionService;
    private PlayerService $playerService;
    public function __construct(PerformanceReportService $performanceReportService, CompetitionService $competitionService, PlayerService $playerService)
    {
        $this->performanceReportService = $performanceReportService;
        $this->competitionService = $competitionService;
        $this->playerService = $playerService;
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
        } elseif (isPlayer()){
            $player = $this->getLoggedPLayerUser();
            $playerSkillStats = $this->playerService->skillStatsChart($player);
            $latestMatches = $this->playerService->playerLatestMatch($player);
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
