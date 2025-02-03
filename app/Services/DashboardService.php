<?php

namespace App\Services;

use App\Models\Coach;
use App\Models\Competition;
use App\Models\MatchModel;
use App\Models\Player;
use App\Models\Team;
use App\Models\Training;
use App\Repository\InvoiceRepository;
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

        $totalCompetitions = Competition::count();
        $totalUpcomingMatches = MatchModel::where('status', 'Scheduled')->count();
        $totalUpcomingTrainings = Training::where('status', 'Scheduled')->count();
        $totalRevenues = $this->invoiceRepository->calculateInvoiceByStatus('Paid', sumAmount: true);
        $totalRevenues = $this->formatReadableNumber($totalRevenues);

        $thisMonthTotalPlayers = Player::whereBetween('created_at',[$startMonth,$now])->count();
        $thisMonthTotalCoaches = Coach::whereBetween('created_at',[$startMonth,$now])->count();
        $thisMonthTotalTeams = Team::where('teamSide','Academy Team')->whereBetween('created_at',[$startMonth,$now])->count();

        $thisMonthTotalCompetitions = Competition::whereBetween('created_at',[$startMonth,$now])->count();

        $thisMonthTotalRevenues = $this->invoiceRepository->calculateInvoiceByStatus('Paid', $startMonth, $now, sumAmount: true);
        $revenueGrowth = $this->financialReportService->revenueGrowth();
        $thisMonthTotalRevenues = $this->formatNumber($thisMonthTotalRevenues);

        return compact(
            'totalPlayers',
            'totalCoaches',
            'totalTeams',
            'totalCompetitions',
            'totalUpcomingMatches',
            'totalUpcomingTrainings',
            'totalRevenues',
            'thisMonthTotalPlayers',
            'thisMonthTotalCoaches',
            'thisMonthTotalTeams',
            'thisMonthTotalCompetitions',
            'thisMonthTotalRevenues',
            'revenueGrowth'
            );
    }

    public function playerAgeChart(){
        $results = Team::join('player_teams as pt', 'teams.id', '=', 'pt.teamId')
            ->select('ageGroup', DB::raw('COUNT(playerId) AS total_player'))
            ->where('teamSide', '=', 'Academy Team')
            ->groupBy('ageGroup')
            ->get();

        $label = $results->pluck('ageGroup');
        $data = $results->pluck('total_player');
        return compact('label', 'data');
    }

    public function upcomingMatches(){
        return MatchModel::with('teams')
            ->where('status', 'Scheduled')
            ->orderBy('date')
            ->take(5)
            ->get();
    }

    public function upcomingTrainings(){
        return Training::with('team')
            ->where('status', 'Scheduled')
            ->orderBy('date')
            ->take(4)
            ->get();
    }
}
