<?php

namespace App\Services;

use App\Models\Coach;
use App\Models\EventSchedule;
use App\Models\TeamMatch;
use App\Repository\CoachMatchStatsRepository;
use App\Repository\EventScheduleRepository;
use App\Repository\TeamMatchRepository;
use Carbon\Carbon;

class PerformanceReportService extends Service
{
    private CoachMatchStatsRepository $coachMatchStatsRepository;
    private EventScheduleRepository $eventScheduleRepository;
    private TeamMatchRepository $teamMatchRepository;
    private EventScheduleService $eventScheduleService;
    public function __construct(
        CoachMatchStatsRepository $coachMatchStatsRepository,
        EventScheduleRepository $eventScheduleRepository,
        TeamMatchRepository $teamMatchRepository,
        EventScheduleService $eventScheduleService
    ){
        $this->coachMatchStatsRepository = $coachMatchStatsRepository;
        $this->eventScheduleRepository = $eventScheduleRepository;
        $this->teamMatchRepository = $teamMatchRepository;
        $this->eventScheduleService = $eventScheduleService;
    }
    public function overviewStats(){
        $stats = [
            'teamScore',
            'cleanSheets',
            'teamOwnGoal',
        ];
        $results = ['Win', 'Lose', 'Draw'];
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now();

        foreach ($results as $result){
            $statsData[$result] = $this->teamMatchRepository->getTeamsStats(results: $result);
            $statsData[$result.'ThisMonth'] = $this->teamMatchRepository->getTeamsStats(startDate: $startDate, endDate: $endDate, results: $result);
        }

        $totalWins = TeamMatch::where('resultStatus', 'Win')
            ->whereHas('team', function($q) {
                $q->where('teamSide', 'Academy Team');
            })->count();
//        $prevMonthWins = TeamMatch::where('resultStatus', 'Win')
//            ->whereHas('team', function($q) {
//                $q->where('teamSide', 'Academy Team');
//            })
//            ->whereHas('match', function($q) {
//                $q->whereBetween('date',[Carbon::now()->startOfMonth()->subMonth(1),Carbon::now()->startOfMonth()]);
//            })->count();
        $thisMonthTotalWins = TeamMatch::where('resultStatus', 'Win')
            ->whereHas('team', function($q) {
                $q->where('teamSide', 'Academy Team');
            })
            ->whereHas('match', function($q) {
                $q->whereBetween('date',[Carbon::now()->startOfMonth(),Carbon::now()]);
            })->count();
//        $winsDiff = $thisMonthWins - $prevMonthWins;

        $totalLosses = TeamMatch::where('resultStatus', 'Lose')
            ->whereHas('team', function($q) {
                $q->where('teamSide', 'Academy Team');
            })->count();
        $thisMonthTotalLosses = TeamMatch::where('resultStatus', 'Lose')
            ->whereHas('team', function($q) {
                $q->where('teamSide', 'Academy Team');
            })
            ->whereHas('match', function($q) {
                $q->whereBetween('date',[Carbon::now()->startOfMonth(),Carbon::now()]);
            })->count();

        $totalDraws = TeamMatch::where('resultStatus', 'Draw')
            ->whereHas('team', function($q) {
                $q->where('teamSide', 'Academy Team');
            })->count();
        $thisMonthTotalDraws = TeamMatch::where('resultStatus', 'Draw')
            ->whereHas('team', function($q) {
                $q->where('teamSide', 'Academy Team');
            })
            ->whereHas('match', function($q) {
                $q->whereBetween('date',[Carbon::now()->startOfMonth(),Carbon::now()]);
            })->count();

        $totalMatchPlayed = EventSchedule::whereHas('teams', function($q) {
                $q->where('teamSide', 'Academy Team');
            })
            ->where('status', 'Completed')
            ->where('eventType', 'Match')
            ->count();
        $thisMonthTotalMatchPlayed = EventSchedule::whereHas('teams', function($q) {
                $q->where('teamSide', 'Academy Team');
            })
            ->whereBetween('date',[Carbon::now()->startOfMonth(),Carbon::now()])
            ->where('status', 'Completed')
            ->where('eventType', 'Match')
            ->count();

        $totalGoals = TeamMatch::whereHas('team', function($q) {
                $q->where('teamSide', 'Academy Team');
            })->sum('teamScore');
        $thisMonthTotalGoals = TeamMatch::whereHas('team', function($q) {
                $q->where('teamSide', 'Academy Team');
            })
            ->whereHas('match', function($q) {
                $q->whereBetween('date',[Carbon::now()->startOfMonth(),Carbon::now()]);
            })
            ->sum('teamScore');

        $totalGoalsConceded = TeamMatch::whereHas('team', function($q) {
                $q->where('teamSide', 'Opponent Team');
            })
            ->sum('teamScore');
        $thisMonthTotalGoalsConceded = TeamMatch::whereHas('team', function($q) {
                $q->where('teamSide', 'Opponent Team');
            })
            ->whereHas('match', function($q) {
                $q->whereBetween('date',[Carbon::now()->startOfMonth(),Carbon::now()]);
            })
            ->sum('teamScore');

        $goalsDifference = $totalGoals - $totalGoalsConceded;
        $thisMonthGoalsDifference = $thisMonthTotalGoals - $thisMonthTotalGoalsConceded;

        $totalCleanSheets = TeamMatch::whereHas('team', function($q) {
                $q->where('teamSide', 'Academy Team');
            })
            ->sum('cleanSheets');
        $thisMonthTotalCleanSheets = TeamMatch::whereHas('team', function($q) {
                $q->where('teamSide', 'Academy Team');
            })
            ->whereHas('match', function($q) {
                $q->whereBetween('date',[Carbon::now()->startOfMonth(),Carbon::now()]);
            })
            ->sum('cleanSheets');

        $totalOwnGoals = TeamMatch::whereHas('team', function($q) {
                $q->where('teamSide', 'Academy Team');
            })
            ->sum('teamOwnGoal');
        $thisMonthTotalOwnGoals = TeamMatch::whereHas('team', function($q) {
                $q->where('teamSide', 'Academy Team');
            })
            ->whereHas('match', function($q) {
                $q->whereBetween('date',[Carbon::now()->startOfMonth(),Carbon::now()]);
            })
            ->sum('teamOwnGoal');

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
        );
    }

    public function coachOverviewStats($coach){
        $totalMatchPlayed = $this->coachMatchStatsRepository->totalMatchPlayed($coach);
        $thisMonthTotalMatchPlayed = $this->coachMatchStatsRepository->thisMonthTotalMatchPlayed($coach);

        $totalGoals =  $this->coachMatchStatsRepository->totalGoals($coach);
        $thisMonthTotalGoals = $this->coachMatchStatsRepository->thisMonthTotalGoals($coach);

        $totalGoalsConceded = $this->coachMatchStatsRepository->totalGoalsConceded($coach);
        $thisMonthTotalGoalsConceded = $this->coachMatchStatsRepository->thisMonthTotalGoalsConceded($coach);

        $totalCleanSheets = $this->coachMatchStatsRepository->totalCleanSheets($coach);
        $thisMonthTotalCleanSheets = $this->coachMatchStatsRepository->thisMonthTotalCleanSheets($coach);

        $totalOwnGoals = $this->coachMatchStatsRepository->totalOwnGoals($coach);
        $thisMonthTotalOwnGoals = $this->coachMatchStatsRepository->thisMonthTotalOwnGoals($coach);

        $totalWins = $this->coachMatchStatsRepository->totalWins($coach);
        $thisMonthTotalWins = $this->coachMatchStatsRepository->thisMonthTotalWins($coach);

        $totalLosses = $this->coachMatchStatsRepository->totalLosses($coach);
        $thisMonthTotalLosses = $this->coachMatchStatsRepository->thisMonthTotalLosses($coach);

        $totalDraws = $this->coachMatchStatsRepository->totalDraws($coach);
        $thisMonthTotalDraws = $this->coachMatchStatsRepository->thisMonthTotalDraws($coach);

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
        );
    }
    public function latestMatch(){
        return $this->eventScheduleRepository->getEvent(['Completed'],'Match', 2);
    }

    public function coachLatestMatch(Coach $coach){
        return $this->eventScheduleRepository->getEventByModel($coach, 'Match', ['Completed', 'Ongoing'], 2);
    }

    public function matchHistory(){
        $data = $this->eventScheduleRepository->getEvent(['Completed'], 'Match');
        return $this->eventScheduleService->makeDataTablesMatch($data);
    }
    public function modelMatchHistory($model){
        $data = $this->eventScheduleRepository->getEventByModel($model, 'Match', ['Completed', 'Ongoing']);
        return $this->eventScheduleService->makeDataTablesMatch($data);
    }
}
