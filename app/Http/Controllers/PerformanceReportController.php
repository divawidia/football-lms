<?php

namespace App\Http\Controllers;

use App\Services\CoachService;
use App\Services\CompetitionService;
use App\Services\PerformanceReportService;
use App\Services\PlayerService;
use App\Services\TeamService;
use Carbon\Carbon;

class PerformanceReportController extends Controller
{
    private PerformanceReportService $performanceReportService;
    private TeamService $teamService;
    private CoachService $coachService;
    private PlayerService $playerService;
    public function __construct(
        PerformanceReportService $performanceReportService,
        TeamService $teamService,
        CoachService $coachService,
        PlayerService $playerService
    )
    {
        $this->performanceReportService = $performanceReportService;
        $this->teamService = $teamService;
        $this->coachService = $coachService;
        $this->playerService = $playerService;
    }

    public function adminIndex()
    {
        $StartDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now();

        return view('pages.academies.reports.performance.admin-index', [
            'winRate' => $this->performanceReportService->winRate(),
            'winRateThisMonth' => $this->performanceReportService->winRate(startDate: $StartDate, endDate: $endDate),
            'totalMatchPlayed' => $this->performanceReportService->totalMatchPlayed(),
            'totalMatchPlayedThisMonth' => $this->performanceReportService->totalMatchPlayed(startDate: $StartDate, endDate: $endDate),
            'teamWins' => $this->performanceReportService->teamWins(),
            'teamWinsThisMonth' => $this->performanceReportService->teamWins(startDate: $StartDate, endDate: $endDate),
            'teamLosses' => $this->performanceReportService->teamLosses(),
            'teamLossesThisMonth' => $this->performanceReportService->teamLosses(startDate: $StartDate, endDate: $endDate),
            'goalScored' => $this->performanceReportService->goalScored(),
            'goalScoredThisMonth' => $this->performanceReportService->goalScored(startDate: $StartDate, endDate: $endDate),
            'teamOwnGoal' => $this->performanceReportService->teamOwnGoal(),
            'teamOwnGoalThisMonth' => $this->performanceReportService->teamOwnGoal(startDate: $StartDate, endDate: $endDate),
            'cleanSheets' => $this->performanceReportService->cleanSheets(),
            'cleanSheetsThisMonth' => $this->performanceReportService->cleanSheets(startDate: $StartDate, endDate: $endDate),
            'goalConceded' => $this->performanceReportService->goalConceded(),
            'goalConcededThisMonth' => $this->performanceReportService->goalConceded(startDate: $StartDate, endDate: $endDate),
            'goalDifference' => $this->performanceReportService->goalDifference(),
            'goalDifferenceThisMonth' => $this->performanceReportService->goalDifference(startDate: $StartDate, endDate: $endDate),
            'teamDraws' => $this->performanceReportService->teamDraws(),
            'teamDrawsThisMonth' => $this->performanceReportService->teamDraws(startDate: $StartDate, endDate: $endDate),
            'teamShotOnTarget' => $this->performanceReportService->teamShotOnTarget(),
            'teamShotOnTargetThisMonth' => $this->performanceReportService->teamShotOnTarget(startDate: $StartDate, endDate: $endDate),
            'teamShots' => $this->performanceReportService->teamShots(),
            'teamShotsThisMonth' => $this->performanceReportService->teamShots(startDate: $StartDate, endDate: $endDate),
            'teamTouches' => $this->performanceReportService->teamTouches(),
            'teamTouchesThisMonth' => $this->performanceReportService->teamTouches(startDate: $StartDate, endDate: $endDate),
            'teamTackles' => $this->performanceReportService->teamTackles(),
            'teamTacklesThisMonth' => $this->performanceReportService->teamTackles(startDate: $StartDate, endDate: $endDate),
            'teamClearances' => $this->performanceReportService->teamClearances(),
            'teamClearancesThisMonth' => $this->performanceReportService->teamClearances(startDate: $StartDate, endDate: $endDate),
            'teamCorners' => $this->performanceReportService->teamCorners(),
            'teamCornersThisMonth' => $this->performanceReportService->teamCorners(startDate: $StartDate, endDate: $endDate),
            'teamOffsides' => $this->performanceReportService->teamOffsides(),
            'teamOffsidesThisMonth' => $this->performanceReportService->teamOffsides(startDate: $StartDate, endDate: $endDate),
            'teamYellowCards' => $this->performanceReportService->teamYellowCards(),
            'teamYellowCardsThisMonth' => $this->performanceReportService->teamYellowCards(startDate: $StartDate, endDate: $endDate),
            'teamRedCards' => $this->performanceReportService->teamRedCards(),
            'teamRedCardsThisMonth' => $this->performanceReportService->teamRedCards(startDate: $StartDate, endDate: $endDate),
            'teamFoulsConceded' => $this->performanceReportService->teamFoulsConceded(),
            'teamFoulsConcededThisMonth' => $this->performanceReportService->teamFoulsConceded(startDate: $StartDate, endDate: $endDate),
            'teamPasses' => $this->performanceReportService->teamPasses(),
            'teamPassesThisMonth' => $this->performanceReportService->teamPasses(startDate: $StartDate, endDate: $endDate),
        ]);
    }

    public function coachIndex()
    {
        $StartDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now();
        $coach = $this->getLoggedCoachUser();

        return view('pages.academies.reports.performance.coach-index', [
            'winRate' => $this->performanceReportService->winRate(),
            'winRateThisMonth' => $this->performanceReportService->winRate(startDate: $StartDate, endDate: $endDate),
            'totalMatchPlayed' => $this->coachService->totalMatchPlayed($coach),
            'totalMatchPlayedThisMonth' => $this->coachService->totalMatchPlayed($coach, startDate: $StartDate, endDate: $endDate),
            'teamWins' => $this->coachService->wins($coach),
            'teamWinsThisMonth' => $this->coachService->wins($coach, startDate: $StartDate, endDate: $endDate),
            'teamLosses' => $this->coachService->lose($coach),
            'teamLossesThisMonth' => $this->coachService->lose($coach, startDate: $StartDate, endDate: $endDate),
            'teamDraws' => $this->coachService->draw($coach),
            'teamDrawsThisMonth' => $this->coachService->draw($coach, startDate: $StartDate, endDate: $endDate),
            'goalScored' => $this->coachService->totalGoals($coach),
            'goalScoredThisMonth' => $this->coachService->totalGoals($coach, startDate: $StartDate, endDate: $endDate),
            'teamOwnGoal' => $this->performanceReportService->teamOwnGoal(),
            'teamOwnGoalThisMonth' => $this->performanceReportService->teamOwnGoal(startDate: $StartDate, endDate: $endDate),
            'cleanSheets' => $this->coachService->cleanSheets($coach),
            'cleanSheetsThisMonth' => $this->coachService->cleanSheets($coach, startDate: $StartDate, endDate: $endDate),
            'goalConceded' => $this->coachService->goalConceded($coach),
            'goalConcededThisMonth' => $this->coachService->goalConceded($coach, startDate: $StartDate, endDate: $endDate),
            'goalDifference' => $this->coachService->goalsDifference($coach),
            'goalDifferenceThisMonth' => $this->coachService->goalsDifference($coach,startDate: $StartDate, endDate: $endDate),
            'teamShotOnTarget' => $this->coachService->teamShotOnTarget($coach),
            'teamShotOnTargetThisMonth' => $this->coachService->teamShotOnTarget($coach, startDate: $StartDate, endDate: $endDate),
            'teamShots' => $this->coachService->teamShots($coach),
            'teamShotsThisMonth' => $this->coachService->teamShots($coach, startDate: $StartDate, endDate: $endDate),
            'teamTouches' => $this->coachService->teamTouches($coach),
            'teamTouchesThisMonth' => $this->coachService->teamTouches($coach, startDate: $StartDate, endDate: $endDate),
            'teamTackles' => $this->coachService->teamTackles($coach),
            'teamTacklesThisMonth' => $this->coachService->teamTackles($coach, startDate: $StartDate, endDate: $endDate),
            'teamClearances' => $this->coachService->teamClearances($coach),
            'teamClearancesThisMonth' => $this->coachService->teamClearances($coach, startDate: $StartDate, endDate: $endDate),
            'teamCorners' => $this->coachService->teamCorners($coach),
            'teamCornersThisMonth' => $this->coachService->teamCorners($coach, startDate: $StartDate, endDate: $endDate),
            'teamOffsides' => $this->coachService->teamOffsides($coach),
            'teamOffsidesThisMonth' => $this->coachService->teamOffsides($coach, startDate: $StartDate, endDate: $endDate),
            'teamYellowCards' => $this->coachService->teamYellowCards($coach),
            'teamYellowCardsThisMonth' => $this->coachService->teamYellowCards($coach, startDate: $StartDate, endDate: $endDate),
            'teamRedCards' => $this->coachService->teamRedCards($coach),
            'teamRedCardsThisMonth' => $this->coachService->teamRedCards($coach, startDate: $StartDate, endDate: $endDate),
            'teamFoulsConceded' => $this->coachService->teamFoulsConceded($coach),
            'teamFoulsConcededThisMonth' => $this->coachService->teamFoulsConceded($coach, startDate: $StartDate, endDate: $endDate),
            'teamPasses' => $this->coachService->teamPasses($coach),
            'teamPassesThisMonth' => $this->coachService->teamPasses($coach, startDate: $StartDate, endDate: $endDate),
        ]);
    }

    public function playerIndex()
    {
        $player = $this->getLoggedPLayerUser();
        return view('pages.academies.reports.performance.player-index', [
            'playerMatchPlayed' => $this->playerService->playerMatchPlayed($player),
            'playerMatchPlayedThisMonth' => $this->playerService->playerMatchPlayedThisMonth($player),
            'playerStats'=>$this->playerService->playerStats($player),
            'matchResults' => $this->playerService->matchStats($player),
            'winRate' =>$this->playerService->winRate($player),
            'playerSkillStats' => $this->playerService->skillStatsChart($player),
            'performanceReviews' => $player->playerPerformanceReview,
        ]);
    }
}
