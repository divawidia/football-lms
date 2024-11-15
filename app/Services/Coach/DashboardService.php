<?php

namespace App\Services\Coach;

use App\Models\Admin;
use App\Models\Coach;
use App\Models\CoachMatchStat;
use App\Models\Competition;
use App\Models\EventSchedule;
use App\Models\Invoice;
use App\Models\Player;
use App\Models\Team;
use App\Models\TeamMatch;
use App\Repository\CoachMatchStatsRepository;
use App\Repository\CoachRepository;
use App\Repository\EventScheduleRepository;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Number;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService extends CoachService
{
    private Coach $coach;
    private CoachMatchStatsRepository $coachMatchStatsRepository;
    private EventScheduleRepository $eventScheduleRepository;
    public function __construct(Coach $coach, CoachMatchStatsRepository $coachMatchStatsRepository, EventScheduleRepository $eventScheduleRepository){
        $this->coach = $coach;
        $this->coachMatchStatsRepository = $coachMatchStatsRepository;
        $this->eventScheduleRepository = $eventScheduleRepository;
    }

    public function overviewStats(){
        $teamsManaged = $this->managedTeams($this->coach);

        $totalMatchPlayed = $this->coachMatchStatsRepository->totalMatchPlayed($this->coach);
        $thisMonthTotalMatchPlayed = $this->coachMatchStatsRepository->thisMonthTotalMatchPlayed($this->coach);

        $totalGoals =  $this->coachMatchStatsRepository->totalGoals($this->coach);
        $thisMonthTotalGoals = $this->coachMatchStatsRepository->thisMonthTotalGoals($this->coach);

        $totalGoalsConceded = $this->coachMatchStatsRepository->totalGoalsConceded($this->coach);
        $thisMonthTotalGoalsConceded = $this->coachMatchStatsRepository->thisMonthTotalGoalsConceded($this->coach);

        $totalCleanSheets = $this->coachMatchStatsRepository->totalCleanSheets($this->coach);
        $thisMonthTotalCleanSheets = $this->coachMatchStatsRepository->thisMonthTotalCleanSheets($this->coach);

        $totalOwnGoals = $this->coachMatchStatsRepository->totalOwnGoals($this->coach);
        $thisMonthTotalOwnGoals = $this->coachMatchStatsRepository->thisMonthTotalOwnGoals($this->coach);

        $totalWins = $this->coachMatchStatsRepository->totalWins($this->coach);
        $thisMonthTotalWins = $this->coachMatchStatsRepository->thisMonthTotalWins($this->coach);

        $totalLosses = $this->coachMatchStatsRepository->totalLosses($this->coach);
        $thisMonthTotalLosses = $this->coachMatchStatsRepository->thisMonthTotalLosses($this->coach);

        $totalDraws = $this->coachMatchStatsRepository->totalDraws($this->coach);
        $thisMonthTotalDraws = $this->coachMatchStatsRepository->thisMonthTotalDraws($this->coach);

        $goalsDifference = $totalGoals - $totalGoalsConceded;
        $thisMonthGoalsDifference = $thisMonthTotalGoals - $thisMonthTotalGoalsConceded;

        return compact(
            'totalMatchPlayed',
            'totalGoals',
            'totalGoalsConceded',
            'goalsDifference',
            'totalCleanSheets',
            'totalOwnGoals',
            'totalWins',
            'totalLosses',
            'totalDraws',
            'thisMonthTotalMatchPlayed',
            'thisMonthTotalGoals',
            'thisMonthTotalGoalsConceded',
            'thisMonthGoalsDifference',
            'thisMonthTotalCleanSheets',
            'thisMonthTotalOwnGoals',
            'thisMonthTotalWins',
            'thisMonthTotalLosses',
            'thisMonthTotalDraws',
            'teamsManaged'
        );
    }

    public function latestMatch()
    {
        $teams = $this->coach->teams;
        $latestMatch = collect();
        foreach ($teams as $team){
            $matches = $this->eventScheduleRepository->getTeamsEvents($team, 'Match', 'Completed', true, 2);
            $latestMatch = $latestMatch->merge($matches);
        }

        return $latestMatch;
    }

    public function upcomingMatch()
    {
        $teams = $this->coach->teams;
        $upcoming = collect();
        foreach ($teams as $team){
            $matches = $this->eventScheduleRepository->getTeamsEvents($team, 'Match', 'Scheduled', true, 2);
            $upcoming = $upcoming->merge($matches);
        }
        return $upcoming;
    }

    public function upcomingTraining()
    {
        $teams = $this->coach->teams;
        $upcoming = collect();
        foreach ($teams as $team){
            $matches = $this->eventScheduleRepository->getTeamsEvents($team, 'Training', 'Scheduled', true, 2);
            $upcoming = $upcoming->merge($matches);
        }
        return $upcoming;
    }

}
