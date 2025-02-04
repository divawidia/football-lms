<?php

namespace App\Services;

use App\Models\Coach;
use App\Models\MatchModel;
use App\Models\Team;
use App\Models\TeamMatch;
use App\Repository\CoachMatchStatsRepository;
use App\Repository\MatchRepository;
use App\Repository\TeamMatchRepository;
use Carbon\Carbon;

class PerformanceReportService extends Service
{
    private MatchRepository $matchRepository;
    private TeamMatchRepository $teamMatchRepository;
    public function __construct(
        MatchRepository           $matchRepository,
        TeamMatchRepository       $teamMatchRepository,
    ){
        $this->matchRepository = $matchRepository;
        $this->teamMatchRepository = $teamMatchRepository;
    }

    public function goalScored(Team $team = null, $startDate = null, $endDate = null)
    {
        return $this->teamMatchRepository->getTeamsStats($team, $startDate, $endDate, stats: 'goalScored');
    }
    public function cleanSheets(Team $team = null, $startDate = null, $endDate = null)
    {
        return $this->teamMatchRepository->getTeamsStats($team, $startDate, $endDate, stats: 'cleanSheets');
    }
    public function teamOwnGoal(Team $team = null, $startDate = null, $endDate = null)
    {
        return $this->teamMatchRepository->getTeamsStats($team, $startDate, $endDate, stats: 'teamOwnGoal');
    }
    public function goalConceded(Team $team = null, $startDate = null, $endDate = null)
    {
        return $this->teamMatchRepository->getTeamsStats($team, $startDate, $endDate, stats: 'goalConceded');
    }
    public function goalDifference(Team $team = null, $startDate = null, $endDate = null)
    {
        return $this->goalScored($team, $startDate, $endDate) - $this->goalConceded($team, $startDate, $endDate);
    }


    public function teamShotOnTarget(Team $team = null, $startDate = null, $endDate = null)
    {
        return $this->teamMatchRepository->getTeamsStats($team, $startDate, $endDate, stats: 'teamShotOnTarget');
    }
    public function teamShots(Team $team = null, $startDate = null, $endDate = null)
    {
        return $this->teamMatchRepository->getTeamsStats($team, $startDate, $endDate, stats: 'teamShots');
    }
    public function teamTouches(Team $team = null, $startDate = null, $endDate = null)
    {
        return $this->teamMatchRepository->getTeamsStats($team, $startDate, $endDate, stats: 'teamTouches');
    }
    public function teamTackles(Team $team = null, $startDate = null, $endDate = null)
    {
        return $this->teamMatchRepository->getTeamsStats($team, $startDate, $endDate, stats: 'teamTackles');
    }
    public function teamClearances(Team $team = null, $startDate = null, $endDate = null)
    {
        return $this->teamMatchRepository->getTeamsStats($team, $startDate, $endDate, stats: 'teamClearances');
    }
    public function teamCorners(Team $team = null, $startDate = null, $endDate = null)
    {
        return $this->teamMatchRepository->getTeamsStats($team, $startDate, $endDate, stats: 'teamCorners');
    }
    public function teamOffsides(Team $team = null, $startDate = null, $endDate = null)
    {
        return $this->teamMatchRepository->getTeamsStats($team, $startDate, $endDate, stats: 'teamOffsides');
    }
    public function teamYellowCards(Team $team = null, $startDate = null, $endDate = null)
    {
        return $this->teamMatchRepository->getTeamsStats($team, $startDate, $endDate, stats: 'teamYellowCards');
    }
    public function teamRedCards(Team $team = null, $startDate = null, $endDate = null)
    {
        return $this->teamMatchRepository->getTeamsStats($team, $startDate, $endDate, stats: 'teamRedCards');
    }
    public function teamFoulsConceded(Team $team = null, $startDate = null, $endDate = null)
    {
        return $this->teamMatchRepository->getTeamsStats($team, $startDate, $endDate, stats: 'teamFoulsConceded');
    }
    public function teamPasses(Team $team = null, $startDate = null, $endDate = null)
    {
        return $this->teamMatchRepository->getTeamsStats($team, $startDate, $endDate, stats: 'teamPasses');
    }


    public function teamWins(Team $team = null, $startDate = null, $endDate = null)
    {
        return $this->teamMatchRepository->getTeamsStats($team, $startDate, $endDate, results: 'Win');
    }
    public function teamLosses(Team $team = null, $startDate = null, $endDate = null)
    {
        return $this->teamMatchRepository->getTeamsStats($team, $startDate, $endDate, results: 'Lose');
    }
    public function teamDraws(Team $team = null, $startDate = null, $endDate = null)
    {
        return $this->teamMatchRepository->getTeamsStats($team, $startDate, $endDate, results: 'Draw');
    }
    public function totalMatchPlayed(Team $team = null, $startDate = null, $endDate = null)
    {
        return $this->matchRepository->getAll(relations: [], teams: $team, status: ['Completed'], startDate: $startDate, endDate: $endDate)->count();
    }
    public function winRate(Team $team = null, $startDate = null, $endDate = null)
    {
        $totalMatch = $this->totalMatchPlayed($team, $startDate, $endDate);
        $wins = $this->teamWins($team, $startDate, $endDate);
        ($totalMatch > 0) ? $winRate = ( $wins /$totalMatch) * 100 : $winRate = 0; // check if totalMatch is 0 then set win rate to 0
        return round($winRate, 2);
    }

}
