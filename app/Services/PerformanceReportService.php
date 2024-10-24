<?php

namespace App\Services;

use App\Models\EventSchedule;
use App\Models\Player;
use App\Models\Team;
use App\Models\TeamMatch;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\Facades\DataTables;

class PerformanceReportService extends Service
{
    public function overviewStats(){
        $wins = TeamMatch::where('resultStatus', 'Win')
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
        $thisMonthWins = TeamMatch::where('resultStatus', 'Win')
            ->whereHas('team', function($q) {
                $q->where('teamSide', 'Academy Team');
            })
            ->whereHas('match', function($q) {
                $q->whereBetween('date',[Carbon::now()->startOfMonth(),Carbon::now()]);
            })->count();
//        $winsDiff = $thisMonthWins - $prevMonthWins;

        $losses = TeamMatch::where('resultStatus', 'Lose')
            ->whereHas('team', function($q) {
                $q->where('teamSide', 'Academy Team');
            })->count();
        $thisMonthLosses = TeamMatch::where('resultStatus', 'Lose')
            ->whereHas('team', function($q) {
                $q->where('teamSide', 'Academy Team');
            })
            ->whereHas('match', function($q) {
                $q->whereBetween('date',[Carbon::now()->startOfMonth(),Carbon::now()]);
            })->count();

        $draws = TeamMatch::where('resultStatus', 'Draw')
            ->whereHas('team', function($q) {
                $q->where('teamSide', 'Academy Team');
            })->count();
        $thisMonthDraws = TeamMatch::where('resultStatus', 'Draw')
            ->whereHas('team', function($q) {
                $q->where('teamSide', 'Academy Team');
            })
            ->whereHas('match', function($q) {
                $q->whereBetween('date',[Carbon::now()->startOfMonth(),Carbon::now()]);
            })->count();

        $matchPlayed = EventSchedule::whereHas('teams', function($q) {
                $q->where('teamSide', 'Academy Team');
            })
            ->where('status', '0')
            ->where('eventType', 'Match')
            ->count();
        $thisMonthMatchPlayed = EventSchedule::whereHas('teams', function($q) {
                $q->where('teamSide', 'Academy Team');
            })
            ->whereBetween('date',[Carbon::now()->startOfMonth(),Carbon::now()])
            ->where('status', '0')
            ->where('eventType', 'Match')
            ->count();

        $goals = TeamMatch::whereHas('team', function($q) {
                $q->where('teamSide', 'Academy Team');
            })->sum('teamScore');
        $thisMonthGoals = TeamMatch::whereHas('team', function($q) {
                $q->where('teamSide', 'Academy Team');
            })
            ->whereHas('match', function($q) {
                $q->whereBetween('date',[Carbon::now()->startOfMonth(),Carbon::now()]);
            })
            ->sum('teamScore');

        $goalsConceded = TeamMatch::whereHas('team', function($q) {
                $q->where('teamSide', 'Opponent Team');
            })
            ->sum('teamScore');
        $thisMonthGoalsConceded = TeamMatch::whereHas('team', function($q) {
                $q->where('teamSide', 'Opponent Team');
            })
            ->whereHas('match', function($q) {
                $q->whereBetween('date',[Carbon::now()->startOfMonth(),Carbon::now()]);
            })
            ->sum('teamScore');

        $goalsDifference = $goals - $goalsConceded;
        $thisMonthGoalsDifference = $thisMonthGoals - $thisMonthGoalsConceded;

        $cleanSheets = TeamMatch::whereHas('team', function($q) {
                $q->where('teamSide', 'Academy Team');
            })
            ->sum('cleanSheets');
        $thisMonthCleanSheets = TeamMatch::whereHas('team', function($q) {
                $q->where('teamSide', 'Academy Team');
            })
            ->whereHas('match', function($q) {
                $q->whereBetween('date',[Carbon::now()->startOfMonth(),Carbon::now()]);
            })
            ->sum('cleanSheets');

        $ownGoals = TeamMatch::whereHas('team', function($q) {
                $q->where('teamSide', 'Academy Team');
            })
            ->sum('teamOwnGoal');
        $thisMonthOwnGoals = TeamMatch::whereHas('team', function($q) {
                $q->where('teamSide', 'Academy Team');
            })
            ->whereHas('match', function($q) {
                $q->whereBetween('date',[Carbon::now()->startOfMonth(),Carbon::now()]);
            })
            ->sum('teamOwnGoal');

        return compact(
            'wins',
            'thisMonthWins',
            'losses',
            'thisMonthLosses',
            'draws',
            'thisMonthDraws',
            'matchPlayed',
            'thisMonthMatchPlayed',
            'goals',
            'thisMonthGoals',
            'goalsConceded',
            'thisMonthGoalsConceded',
            'goalsDifference',
            'thisMonthGoalsDifference',
            'cleanSheets',
            'thisMonthCleanSheets',
            'ownGoals',
            'thisMonthOwnGoals');
    }
    public function coachOverviewStats($coach){
        $teams = $this->coachManagedTeams($coach);

        $wins = TeamMatch::where('resultStatus', 'Win')
            ->whereHas('team', function($q)  use($teams){
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $teams[0]->id);

                // if teams are more than 1 then iterate more
                if (count($teams)>1){
                    for ($i = 1; $i < count($teams); $i++){
                        $q->orWhere('teamId', $teams[$i]->id);
                    }
                }
            })->count();

        $thisMonthWins = TeamMatch::where('resultStatus', 'Win')
            ->whereHas('team', function($q) use($teams){
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $teams[0]->id);

                // if teams are more than 1 then iterate more
                if (count($teams)>1){
                    for ($i = 1; $i < count($teams); $i++){
                        $q->orWhere('teamId', $teams[$i]->id);
                    }
                }
            })
            ->whereHas('match', function($q) {
                $q->whereBetween('date',[Carbon::now()->startOfMonth(),Carbon::now()]);
            })->count();
//        $winsDiff = $thisMonthWins - $prevMonthWins;

        $losses = TeamMatch::where('resultStatus', 'Lose')
            ->whereHas('team', function($q) use($teams){
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $teams[0]->id);

                // if teams are more than 1 then iterate more
                if (count($teams)>1){
                    for ($i = 1; $i < count($teams); $i++){
                        $q->orWhere('teamId', $teams[$i]->id);
                    }
                }
            })->count();
        $thisMonthLosses = TeamMatch::where('resultStatus', 'Lose')
            ->whereHas('team', function($q) use($teams){
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $teams[0]->id);

                // if teams are more than 1 then iterate more
                if (count($teams)>1){
                    for ($i = 1; $i < count($teams); $i++){
                        $q->orWhere('teamId', $teams[$i]->id);
                    }
                }
            })
            ->whereHas('match', function($q) use($teams){
                $q->whereBetween('date',[Carbon::now()->startOfMonth(),Carbon::now()]);
            })->count();

        $draws = TeamMatch::where('resultStatus', 'Draw')
            ->whereHas('team', function($q) use($teams){
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $teams[0]->id);

                // if teams are more than 1 then iterate more
                if (count($teams)>1){
                    for ($i = 1; $i < count($teams); $i++){
                        $q->orWhere('teamId', $teams[$i]->id);
                    }
                }
            })->count();
        $thisMonthDraws = TeamMatch::where('resultStatus', 'Draw')
            ->whereHas('team', function($q) use($teams){
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $teams[0]->id);

                // if teams are more than 1 then iterate more
                if (count($teams)>1){
                    for ($i = 1; $i < count($teams); $i++){
                        $q->orWhere('teamId', $teams[$i]->id);
                    }
                }
            })
            ->whereHas('match', function($q) use($teams){
                $q->whereBetween('date',[Carbon::now()->startOfMonth(),Carbon::now()]);
            })->count();

        $matchPlayed = EventSchedule::whereHas('teams', function($q) use($teams){
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $teams[0]->id);

                // if teams are more than 1 then iterate more
                if (count($teams)>1){
                    for ($i = 1; $i < count($teams); $i++){
                        $q->orWhere('teamId', $teams[$i]->id);
                    }
                }
            })
            ->where('status', '0')
            ->where('eventType', 'Match')
            ->count();
        $thisMonthMatchPlayed = EventSchedule::whereHas('teams', function($q) use($teams){
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $teams[0]->id);

                // if teams are more than 1 then iterate more
                if (count($teams)>1){
                    for ($i = 1; $i < count($teams); $i++){
                        $q->orWhere('teamId', $teams[$i]->id);
                    }
                }
            })
            ->whereBetween('date',[Carbon::now()->startOfMonth(),Carbon::now()])
            ->where('status', '0')
            ->where('eventType', 'Match')
            ->count();

        $goals = TeamMatch::whereHas('team', function($q) use($teams){
            $q->where('teamSide', 'Academy Team');
            $q->where('teamId', $teams[0]->id);

            // if teams are more than 1 then iterate more
            if (count($teams)>1){
                for ($i = 1; $i < count($teams); $i++){
                    $q->orWhere('teamId', $teams[$i]->id);
                }
            }
        })->sum('teamScore');
        $thisMonthGoals = TeamMatch::whereHas('team', function($q) use($teams){
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $teams[0]->id);

                // if teams are more than 1 then iterate more
                if (count($teams)>1){
                    for ($i = 1; $i < count($teams); $i++){
                        $q->orWhere('teamId', $teams[$i]->id);
                    }
                }
            })
            ->whereHas('match', function($q) {
                $q->whereBetween('date',[Carbon::now()->startOfMonth(),Carbon::now()]);
            })
            ->sum('teamScore');

        $goalsConceded = TeamMatch::whereHas('team', function($q) use($teams){
                $q->where('teamSide', 'Opponent Team');
                $q->where('teamId', $teams[0]->id);

                // if teams are more than 1 then iterate more
                if (count($teams)>1){
                    for ($i = 1; $i < count($teams); $i++){
                        $q->orWhere('teamId', $teams[$i]->id);
                    }
                }
            })
            ->sum('teamScore');
        $thisMonthGoalsConceded = TeamMatch::whereHas('team', function($q) use($teams){
                $q->where('teamSide', 'Opponent Team');
                $q->where('teamId', $teams[0]->id);

                // if teams are more than 1 then iterate more
                if (count($teams)>1){
                    for ($i = 1; $i < count($teams); $i++){
                        $q->orWhere('teamId', $teams[$i]->id);
                    }
                }
            })
            ->whereHas('match', function($q) {
                $q->whereBetween('date',[Carbon::now()->startOfMonth(),Carbon::now()]);
            })
            ->sum('teamScore');

        $goalsDifference = $goals - $goalsConceded;
        $thisMonthGoalsDifference = $thisMonthGoals - $thisMonthGoalsConceded;

        $cleanSheets = TeamMatch::whereHas('team', function($q) use($teams){
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $teams[0]->id);

                // if teams are more than 1 then iterate more
                if (count($teams)>1){
                    for ($i = 1; $i < count($teams); $i++){
                        $q->orWhere('teamId', $teams[$i]->id);
                    }
                }
            })
            ->sum('cleanSheets');
        $thisMonthCleanSheets = TeamMatch::whereHas('team', function($q) use($teams){
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $teams[0]->id);

                // if teams are more than 1 then iterate more
                if (count($teams)>1){
                    for ($i = 1; $i < count($teams); $i++){
                        $q->orWhere('teamId', $teams[$i]->id);
                    }
                }
            })
            ->whereHas('match', function($q) {
                $q->whereBetween('date',[Carbon::now()->startOfMonth(),Carbon::now()]);
            })
            ->sum('cleanSheets');

        $ownGoals = TeamMatch::whereHas('team', function($q) use($teams){
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $teams[0]->id);

                // if teams are more than 1 then iterate more
                if (count($teams)>1){
                    for ($i = 1; $i < count($teams); $i++){
                        $q->orWhere('teamId', $teams[$i]->id);
                    }
                }
            })
            ->sum('teamOwnGoal');
        $thisMonthOwnGoals = TeamMatch::whereHas('team', function($q) use($teams){
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $teams[0]->id);

                // if teams are more than 1 then iterate more
                if (count($teams)>1){
                    for ($i = 1; $i < count($teams); $i++){
                        $q->orWhere('teamId', $teams[$i]->id);
                    }
                }
            })
            ->whereHas('match', function($q) {
                $q->whereBetween('date',[Carbon::now()->startOfMonth(),Carbon::now()]);
            })
            ->sum('teamOwnGoal');

        return compact(
            'wins',
            'thisMonthWins',
            'losses',
            'thisMonthLosses',
            'draws',
            'thisMonthDraws',
            'matchPlayed',
            'thisMonthMatchPlayed',
            'goals',
            'thisMonthGoals',
            'goalsConceded',
            'thisMonthGoalsConceded',
            'goalsDifference',
            'thisMonthGoalsDifference',
            'cleanSheets',
            'thisMonthCleanSheets',
            'ownGoals',
            'thisMonthOwnGoals');
    }
    public function latestMatch(){
        return EventSchedule::with('teams', 'competition')
            ->where('eventType', 'Match')
            ->where('status', '0')
            ->orderBy('date', 'desc')
            ->take(2)
            ->get();
    }
    public function matchHistory(){
        $data = EventSchedule::with('teams', 'competition')
            ->where('eventType', 'Match')
            ->where('status', '0')
            ->get();

        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                return '
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
                $date = date('M d, Y', strtotime($item->date));
                $startTime = date('h:i A', strtotime($item->startTime));
                $endTime = date('h:i A', strtotime($item->endTime));
                return $date.' ('.$startTime.' - '.$endTime.')';
            })
            ->rawColumns(['action','team', 'score', 'competition','opponentTeam','date'])
            ->make();
    }
}
