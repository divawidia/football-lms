<?php

namespace App\Services\Coach;

use App\Models\Admin;
use App\Models\Coach;
use App\Models\Competition;
use App\Models\EventSchedule;
use App\Models\Invoice;
use App\Models\Player;
use App\Models\Team;
use App\Models\TeamMatch;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Number;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    private $coachId;
    public function __construct($coachId){
        $this->coachId = $coachId;
    }
    public function managedTeams(){
        return Team::with('coaches')
            ->whereHas('coaches', function($q) {
                $q->where('coachId', $this->coachId);
            })->get();
    }
    public function overviewStats(){
        $teamsManaged = $this->managedTeams();

        $totalMatchPlayed = 0;
        $totalGoals = 0;
        $totalGoalsConceded = 0;
        $totalCleanSheets = 0;
        $totalOwnGoals = 0;
        $totalWins = 0;
        $totalLosses = 0;
        $totalDraws = 0;

        foreach ($teamsManaged as $team){
            $matchPlayed = EventSchedule::whereHas('teams', function($q) use ($team) {
                    $q->where('teamSide', 'Academy Team');
                    $q->where('teamId', $team->id);
                })
                ->where('status', '0')
                ->where('eventType', 'Match')
                ->count();
            $goals = TeamMatch::whereHas('team', function($q) use ($team) {
                    $q->where('teamSide', 'Academy Team');
                    $q->where('teamId', $team->id);
                })->sum('teamScore');
            $goalsConceded = TeamMatch::whereHas('team', function($q) use ($team) {
                    $q->where('teamSide', 'Opponent Team');
                    $q->where('teamId', $team->id);
                })
                ->sum('teamScore');
            $cleanSheets = TeamMatch::whereHas('team', function($q) use ($team) {
                    $q->where('teamSide', 'Academy Team');
                    $q->where('teamId', $team->id);
                })
                ->sum('cleanSheets');
            $ownGoals = TeamMatch::whereHas('team', function($q) use ($team) {
                    $q->where('teamSide', 'Academy Team');
                    $q->where('teamId', $team->id);
                })
                ->sum('teamOwnGoal');
            $wins = TeamMatch::where('resultStatus', 'Win')
                ->whereHas('team', function($q) use ($team) {
                    $q->where('teamSide', 'Academy Team');
                    $q->where('teamId', $team->id);
                })->count();
            $losses = TeamMatch::where('resultStatus', 'Lose')
                ->whereHas('team', function($q) use ($team) {
                    $q->where('teamSide', 'Academy Team');
                    $q->where('teamId', $team->id);
                })->count();
            $draws = TeamMatch::where('resultStatus', 'Draw')
                ->whereHas('team', function($q) use ($team) {
                    $q->where('teamSide', 'Academy Team');
                    $q->where('teamId', $team->id);
                })->count();

            $totalMatchPlayed += $matchPlayed;
            $totalGoals += $goals;
            $totalGoalsConceded += $goalsConceded;
            $totalCleanSheets += $cleanSheets;
            $totalOwnGoals += $ownGoals;
            $totalWins += $wins;
            $totalLosses += $losses;
            $totalDraws += $draws;
        }

        $goalsDifference = $totalGoals - $totalGoalsConceded;

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
            'teamsManaged'
            );
    }

    public function latestMatch(){
        $match = [];

        foreach ($this->managedTeams() as $team){
            $match[] = EventSchedule::with('teams', 'competition')
                ->whereHas('teams', function($q) use ($team) {
                    $q->where('teamId', $team->id);
                })
                ->where('eventType', 'Match')
                ->where('status', '1')
                ->orderBy('date', 'desc')
                ->get();
        }
        return $match;
    }

    public function upcomingMatch(){
        $match = [];

        foreach ($this->managedTeams() as $team){
            $match[] = EventSchedule::with('teams', 'competition')
                ->whereHas('teams', function($q) use ($team) {
                    $q->where('teamId', $team->id);
                })
                ->where('eventType', 'Match')
                ->where('status', '0')
                ->orderBy('date', 'desc')
                ->get();
        }
        return $match;
    }

    public function upcomingTraining(){
        $trainings = [];

        foreach ($this->managedTeams() as $team){
            $trainings[] = EventSchedule::with('teams', 'competition')
                ->whereHas('teams', function($q) use ($team) {
                    $q->where('teamId', $team->id);
                })
                ->where('eventType', 'Training')
                ->where('status', '0')
                ->orderBy('date', 'desc')
                ->get();
        }
        return $trainings;
    }

}
