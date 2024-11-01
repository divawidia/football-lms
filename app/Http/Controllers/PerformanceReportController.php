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
        $playerSkillStats = null;
        $latestTrainings = null;
        $performanceReviews = null;
        $player = null;
        if (isAllAdmin()){
            $latestMatches = $this->performanceReportService->latestMatch();
            $overviewStats = $this->performanceReportService->overviewStats();
            $activeCompetitions = $this->competitionService->getActiveCompetition();
            $allCompetitions = $this->competitionService->index();
            $matchHistoryRoutes = url()->route('admin.performance-report.index');
        } elseif (isCoach()){
            $coach = $this->getLoggedCoachUser();
            $latestMatches = $this->performanceReportService->coachLatestMatch($coach);
            $overviewStats = $this->performanceReportService->coachOverviewStats($coach);
            $activeCompetitions = $this->competitionService->modelTeamsCompetition($coach, '1');
            $allCompetitions = $this->competitionService->modelTeamsCompetition($coach);
            $matchHistoryRoutes = url()->route('coach.performance-report.index');
        } elseif (isPlayer()){
            $player = $this->getLoggedPLayerUser();
            $playerSkillStats = $this->playerService->skillStatsChart($player);
            $latestMatches = $this->playerService->playerLatestMatch($player);
            $latestTrainings = $this->playerService->playerLatestTraining($player);
            $activeCompetitions = $this->competitionService->modelTeamsCompetition($player, '1');
            $allCompetitions = $this->competitionService->modelTeamsCompetition($player);
            $overviewStats = $this->playerService->show($player);
            $performanceReviews = $player->playerPerformanceReview;
            $matchHistoryRoutes = url()->route('player.performance-report.index');
        }


        return view('pages.academies.reports.performance.index', [
            'latestMatches' => $latestMatches,
            'overviewStats' => $overviewStats,
            'activeCompetitions' => $activeCompetitions,
            'allCompetitions' => $allCompetitions,
            'matchHistoryRoutes' => $matchHistoryRoutes,
            'playerSkillStats' => $playerSkillStats,
            'latestTrainings' => $latestTrainings,
            'performanceReviews' => $performanceReviews,
            'player' => $player
        ]);
    }

    public function adminIndex()
    {
        return $this->performanceReportService->matchHistory();
    }

    public function coachIndex(){
        $coach = $this->getLoggedCoachUser();
        return $this->performanceReportService->modelMatchHistory($coach);
    }
    public function playerIndex(){
        $player = $this->getLoggedPLayerUser();
        return $this->performanceReportService->modelMatchHistory($player);
    }

}
