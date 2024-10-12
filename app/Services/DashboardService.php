<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Coach;
use App\Models\Competition;
use App\Models\EventSchedule;
use App\Models\Invoice;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Support\Number;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function overviewStats(){
        $totalPlayers = Player::count();
        $totalCoaches = Coach::count();
        $totalTeams = Team::where('teamSide','Academy Teams')->count();
        $totalAdmins = Admin::count();
        $totalCompetitions = Competition::count();
        $totalUpcomingMatches = EventSchedule::where('eventType', 'Match')->where('status', '1')->count();
        $totalUpcomingTrainings = EventSchedule::where('eventType', 'Training')->where('status', '1')->count();
        $totalRevenues = Invoice::where('status', 'Paid')->sum('ammountDue');
        $totalRevenues = Number::forHumans($totalRevenues, abbreviate: true);

        $totalPaidInvoices = Invoice::where('status', 'Paid')->count();
        $totalOpenInvoices = Invoice::where('status', 'Open')->count();
        $totalPastDueInvoices = Invoice::where('status', 'Past Due')->count();
        $totalUncollectInvoices = Invoice::where('status', 'Uncollectible')->count();

        $sumPaidInvoices = Invoice::where('status', 'Paid')->sum('ammountDue');
        $sumOpenInvoices = Invoice::where('status', 'Open')->sum('ammountDue');
        $sumPastDueInvoices = Invoice::where('status', 'Past Due')->sum('ammountDue');
        $sumUncollectInvoices = Invoice::where('status', 'Uncollectible')->sum('ammountDue');


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
            'thisMonthTotalRevenues',
            'totalOpenInvoices',
            'totalPaidInvoices',
            'totalPastDueInvoices',
            'totalUncollectInvoices'
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

    public function revenueChart(){
        $results = DB::table('invoices')
            ->select(DB::raw('MONTHNAME(created_at) as month'), DB::raw('SUM(ammountDue) AS total_ammount'))
            ->where('status', '=', 'Paid')
            ->groupBy(DB::raw('MONTHNAME(created_at) as month'))
            ->get();

        $label = [];
        $data = [];
        foreach ($results as $result){
            $label[] = $result->month;
            $data[] = $result->total_ammount;
        }

        return compact('label', 'data');
    }


}
