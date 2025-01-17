<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Coach;
use App\Models\Competition;
use App\Models\Match;
use App\Models\Invoice;
use App\Models\Player;
use App\Models\Team;
use App\Repository\InvoiceRepository;
use Illuminate\Support\Number;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService extends Service
{
    private InvoiceRepository $invoiceRepository;
    private FinancialReportService $financialReportService;
    public function __construct(InvoiceRepository $invoiceRepository, FinancialReportService $financialReportService)
    {
        $this->invoiceRepository = $invoiceRepository;
        $this->financialReportService = $financialReportService;
    }

    public function overviewStats(){
        $startMonth = Carbon::now()->startOfMonth();
        $now = Carbon::now();

        $totalPlayers = Player::count();
        $totalCoaches = Coach::count();
        $totalTeams = Team::where('teamSide','Academy Team')->count();
        $totalAdmins = Admin::count();
        $totalCompetitions = Competition::count();
        $totalUpcomingMatches = Match::where('eventType', 'Match')->where('status', '1')->count();
        $totalUpcomingTrainings = Match::where('eventType', 'Training')->where('status', '1')->count();
        $totalRevenues = $this->invoiceRepository->calculateInvoiceByStatus('Paid', sumAmount: true);
        $totalRevenues = $this->formatReadableNumber($totalRevenues);

        $thisMonthTotalPlayers = Player::whereBetween('created_at',[$startMonth,$now])->count();
        $thisMonthTotalCoaches = Coach::whereBetween('created_at',[$startMonth,$now])->count();
        $thisMonthTotalTeams = Team::where('teamSide','Academy Team')->whereBetween('created_at',[$startMonth,$now])->count();
        $thisMonthTotalAdmins = Admin::whereBetween('created_at',[$startMonth,$now])->count();
        $thisMonthTotalCompetitions = Competition::whereBetween('created_at',[$startMonth,$now])->count();

        $thisMonthTotalRevenues = $this->invoiceRepository->calculateInvoiceByStatus('Paid', $startMonth, $now, sumAmount: true);
        $revenueGrowth = $this->financialReportService->revenueGrowth();
        $thisMonthTotalRevenues = $this->formatNumber($thisMonthTotalRevenues);

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
            'revenueGrowth'
            );
    }

    public function playerAgeChart(){
        $results = DB::table('teams')
            ->join('player_teams as pt', 'teams.id', '=', 'pt.teamId')
            ->select('ageGroup', DB::raw('COUNT(playerId) AS total_player'))
            ->where('teamSide', '=', 'Academy Team')
            ->groupBy('ageGroup')
            ->get();

        $label = [];
        $data = [];
        foreach ($results as $result){
            $label[] = $result->ageGroup;
            $data[] = $result->total_player;
        }

        return compact('label', 'data');
    }

    public function upcomingMatches(){
        return Match::with('teams')
            ->where('status', '1')
            ->where('eventType', 'Match')
            ->orderBy('date')
            ->take(5)
            ->get();

    }

    public function upcomingTrainings(){
        return Match::with('teams')
            ->where('status', '1')
            ->where('eventType', 'Training')
            ->orderBy('date')
            ->take(4)
            ->get();

    }


}
