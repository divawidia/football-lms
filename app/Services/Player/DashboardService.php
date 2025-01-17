<?php

namespace App\Services\Player;

use App\Models\Admin;
use App\Models\Coach;
use App\Models\CoachMatchStat;
use App\Models\Competition;
use App\Models\Match;
use App\Models\Invoice;
use App\Models\Player;
use App\Models\Team;
use App\Models\TeamMatch;
use App\Repository\CoachMatchStatsRepository;
use App\Repository\CoachRepository;
use App\Repository\PlayerRepository;
use App\Services\Coach\CoachService;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Number;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService extends CoachService
{
    private Player $player;
    private PlayerRepository $playerRepository;
    public function __construct(PlayerRepository $playerRepository, Player $player){
        $this->playerRepository = $playerRepository;
        $this->player = $player;
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
        $teams = $this->managedTeams($this->coach);
        return Match::with('teams', 'competition')
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
        return Match::with('teams', 'competition')
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
        return Match::with('teams', 'competition')
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
