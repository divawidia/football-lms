<?php

namespace App\Services;

use App\Models\Coach;
use App\Models\EventSchedule;
use App\Models\TeamMatch;
use App\Repository\CoachMatchStatsRepository;
use App\Repository\EventScheduleRepository;
use App\Repository\TeamMatchRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PerformanceReportService extends Service
{
    private CoachMatchStatsRepository $coachMatchStatsRepository;
    private EventScheduleRepository $eventScheduleRepository;
    private TeamMatchRepository $teamMatchRepository;
    public function __construct(CoachMatchStatsRepository $coachMatchStatsRepository, EventScheduleRepository $eventScheduleRepository, TeamMatchRepository $teamMatchRepository){
        $this->coachMatchStatsRepository = $coachMatchStatsRepository;
        $this->eventScheduleRepository = $eventScheduleRepository;
        $this->teamMatchRepository = $teamMatchRepository;
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
            ->where('status', '0')
            ->where('eventType', 'Match')
            ->count();
        $thisMonthTotalMatchPlayed = EventSchedule::whereHas('teams', function($q) {
                $q->where('teamSide', 'Academy Team');
            })
            ->whereBetween('date',[Carbon::now()->startOfMonth(),Carbon::now()])
            ->where('status', '0')
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
        return $this->eventScheduleRepository->getEvent('Match', '0', 2);
    }

    public function coachLatestMatch(Coach $coach){
        return $this->eventScheduleRepository->getEventByModel($coach, 'Match', '0', 2);
    }


    public function matchHistoryDatatables($data)
    {
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                if (isAllAdmin()){
                    $actionBtn ='
                        <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="' . route('match-schedules.edit', $item->id) . '"><span class="material-icons">edit</span> Edit Match</a>
                            <a class="dropdown-item" href="' . route('match-schedules.show', $item->id) . '"><span class="material-icons">visibility</span> View Match</a>
                            <button type="button" class="dropdown-item delete" id="' . $item->id . '">
                                <span class="material-icons">delete</span> Delete Match
                            </button>
                          </div>
                        </div>';
                } elseif (isCoach() || isPlayer()){
                    $actionBtn = '<a class="btn btn-sm btn-outline-secondary" href="' . route('match-schedules.show', $item->id) . '" data-toggle="tooltips" data-placement="bottom" title="View Match Detail">
                                        <span class="material-icons">visibility</span>
                                  </a>';
                }
                return $actionBtn;
            })
            ->editColumn('team', function ($item) {
                return '
                        <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->teams[0]->logo) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->teams[0]->teamName . '</strong></p>
                                            <small class="js-lists-values-email text-50">'.$item->teams[0]->ageGroup.'</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            })
            ->editColumn('opponentTeam', function ($item) {
                return '
                        <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->teams[1]->logo) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->teams[1]->teamName . '</strong></p>
                                            <small class="js-lists-values-email text-50">'.$item->teams[1]->ageGroup.'</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            })
            ->editColumn('score', function ($item){
                return '<p class="mb-0"><strong class="js-lists-values-lead">' .$item->teams[0]->pivot->teamScore . ' - ' . $item->teams[1]->pivot->teamScore.'</strong></p>';
            })
            ->editColumn('competition', function ($item) {
                if ($item->competition){
                    $competition = '
                            <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->competition->logo) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->competition->name . '</strong></p>
                                            <small class="js-lists-values-email text-50">'.$item->competition->type.'</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                }else{
                    $competition = 'No Competition';
                }
                return $competition;
            })
            ->editColumn('date', function ($item) {
                $date = $this->convertToDate($item->date);
                $startTime = $this->convertToTime($item->startTime);
                $endTime = $this->convertToTime($item->endTime);
                return $date.' ('.$startTime.' - '.$endTime.')';
            })
            ->rawColumns(['action','team', 'score', 'competition','opponentTeam','date'])
            ->make();
    }
    public function matchHistory(){
        $data = $this->eventScheduleRepository->getEvent('Match', '0');
        return $this->matchHistoryDatatables($data);
    }
    public function modelMatchHistory($model){
        $data = $this->eventScheduleRepository->getEventByModel($model, 'Match', '0');
        return $this->matchHistoryDatatables($data);
    }
}
