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

class DashboardService extends CoachService
{
    private $coach;
    public function __construct($coach){
        $this->coach = $coach;
    }

    public function overviewStats(){
        $teamsManaged = $this->managedTeams($this->coach);

        $totalMatchPlayed = $this->coach->coachMatchStats()->count();
        $totalGoals =  $this->coach->coachMatchStats()->sum('teamScore');
        $totalGoalsConceded =  $this->coach->coachMatchStats()->sum('goalConceded');
        $totalCleanSheets = $this->coach->coachMatchStats()->sum('cleanSheets');
        $totalOwnGoals = $this->coach->coachMatchStats()->sum('teamOwnGoal');
        $totalWins = $this->coach->coachMatchStats()->where('resultStatus', 'Win')->count();
        $totalLosses = $this->coach->coachMatchStats()->where('resultStatus', 'Lose')->count();
        $totalDraws = $this->coach->coachMatchStats()->where('resultStatus', 'Draw')->count();

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

    public function latestMatch()
    {
        $teams = $this->managedTeams($this->coach);
        return EventSchedule::with('teams', 'competition')
            ->whereHas('teams', function($q) use ($teams){
                $q->where('teamId', $teams[0]->id);

                // if teams are more than 1 then iterate more
                if (count($teams)>1){
                    for ($i = 1; $i < count($teams); $i++){
                        $q->orWhere('teamId', $teams[$i]->id);
                    }
                }
            })
            ->where('eventType', 'Match')
            ->where('status', '0')
            ->orderBy('date', 'desc')
            ->take(2)
            ->get();
    }

    public function upcomingMatch()
    {
        $teams = $this->managedTeams($this->coach);
        return EventSchedule::with('teams', 'competition')
            ->whereHas('teams', function($q) use($teams) {
                $q->where('teamId', $teams[0]->id);

                // if teams are more than 1 then iterate more
                if (count($teams)>1){
                    for ($i = 1; $i < count($teams); $i++){
                        $q->orWhere('teamId', $teams[$i]->id);
                    }
                }
            })
            ->where('eventType', 'Match')
            ->where('status', '1')
            ->orderBy('date', 'desc')
            ->take(2)
            ->get();
    }

    public function upcomingTraining()
    {
        $teams = $this->managedTeams($this->coach);
        return EventSchedule::with('teams', 'competition')
            ->whereHas('teams', function($q) use ($teams) {
                $q->where('teamId', $teams[0]->id);

                // if teams are more than 1 then iterate more
                if (count($teams)>1){
                    for ($i = 1; $i < count($teams); $i++){
                        $q->orWhere('teamId', $teams[$i]->id);
                    }
                }
            })
            ->where('eventType', 'Training')
            ->where('status', '1')
            ->orderBy('date', 'desc')
            ->take(2)
            ->get();
    }

}
