<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Coach;
use App\Models\Competition;
use App\Models\EventSchedule;
use App\Models\Invoice;
use App\Models\Player;
use App\Models\Team;
use App\Models\TeamMatch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function overviewStats(){
        $totalPlayers = Player::count();
        $totalCoaches = Coach::count();
        $totalTeams = Team::where('Academy Teams')->count();
        $totalAdmins = Admin::count();
        $totalCompetitions = Competition::count();
        $totalUpcomingMatches = EventSchedule::where('eventType', 'Match')->where('status', '1')->count();
        $totalUpcomingTrainings = EventSchedule::where('eventType', 'Training')->where('status', '1')->count();
        $totalRevenues = Invoice::where('status', 'Paid')->sum('ammountDue');

//        $prevMonthWins = TeamMatch::where('resultStatus', 'Win')
//            ->whereHas('team', function($q) {
//                $q->where('teamSide', 'Academy Team');
//            })
//            ->whereHas('match', function($q) {
//                $q->whereBetween('date',[Carbon::now()->startOfMonth()->subMonth(1),Carbon::now()->startOfMonth()]);
//            })->count();
        $thisMonthTotalPlayers = Player::whereBetween('created_at',[Carbon::now()->startOfMonth(),Carbon::now()])->count();
        $thisMonthTotalCoaches = Coach::whereBetween('created_at',[Carbon::now()->startOfMonth(),Carbon::now()])->count();
        $thisMonthTotalTeams = Team::whereBetween('created_at',[Carbon::now()->startOfMonth(),Carbon::now()])->count();
        $thisMonthTotalAdmins = Admin::whereBetween('created_at',[Carbon::now()->startOfMonth(),Carbon::now()])->count();
        $thisMonthTotalCompetitions = Competition::whereBetween('created_at',[Carbon::now()->startOfMonth(),Carbon::now()])->count();
        $thisMonthTotalRevenues = Invoice::where('status', 'Paid')
            ->whereBetween('created_at',[Carbon::now()->startOfMonth(),Carbon::now()])
            ->sum('ammountDue');

        return compact(
            'totalPlayers',
            'totalCoaches',
            'totalTeams',
            'totalAdmins',
            'totalCompetitions',
            'totalUpcomingMatches',
            'totalUpcomingTrainings',
            'totalRevenues',
            'thisMonthTotalPlayers',
            'thisMonthTotalCoaches',
            'thisMonthTotalTeams',
            'thisMonthTotalAdmins',
            'thisMonthTotalCompetitions',
            'thisMonthTotalRevenues'
            );
    }

    public function teamAgeDoughnutChart(){
        $results = DB::table('teams')
            ->select('ageGroup', DB::raw('COUNT(ageGroup) AS total'))
            ->where('teamSide', '=', 'Academy Team')
            ->groupBy('ageGroup')
            ->get();

        $label = [];
        $data = [];
        foreach ($results as $result){
            $label[] = $result->ageGroup;
            $data[] = $result->total;
        }

        return compact('label', 'data');
    }


}
