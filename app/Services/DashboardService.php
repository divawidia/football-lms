<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Coach;
use App\Models\Competition;
use App\Models\EventSchedule;
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
    public function __construct(InvoiceRepository $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    public function overviewStats(){
        $startMonth = Carbon::now()->startOfMonth();
        $now = Carbon::now();
        $startOfLastMonth = now()->subMonth()->startOfMonth();
        $endOfLastMonth = now()->subMonth()->endOfMonth();

        $totalPlayers = Player::count();
        $totalCoaches = Coach::count();
        $totalTeams = Team::where('teamSide','Academy Team')->count();
        $totalAdmins = Admin::count();
        $totalCompetitions = Competition::count();
        $totalUpcomingMatches = EventSchedule::where('eventType', 'Match')->where('status', '1')->count();
        $totalUpcomingTrainings = EventSchedule::where('eventType', 'Training')->where('status', '1')->count();
        $totalRevenues = $this->invoiceRepository->calculateInvoiceByStatus('Paid', sumAmount: true);
        $totalRevenues = $this->formatReadableNumber($totalRevenues);

        $thisMonthTotalPlayers = Player::whereBetween('created_at',[$startMonth,$now])->count();
        $thisMonthTotalCoaches = Coach::whereBetween('created_at',[$startMonth,$now])->count();
        $thisMonthTotalTeams = Team::where('teamSide','Academy Team')->whereBetween('created_at',[$startMonth,$now])->count();
        $thisMonthTotalAdmins = Admin::whereBetween('created_at',[$startMonth,$now])->count();
        $thisMonthTotalCompetitions = Competition::whereBetween('created_at',[$startMonth,$now])->count();

        $thisMonthTotalRevenues = $this->invoiceRepository->calculateInvoiceByStatus('Paid', $startMonth, $now, sumAmount: true);
        $previousMonthTotalRevenues = $this->invoiceRepository->calculateInvoiceByStatus('Paid', $startOfLastMonth, $endOfLastMonth, sumAmount: true);
        $revenueGrowth = (($thisMonthTotalRevenues - $previousMonthTotalRevenues) / $previousMonthTotalRevenues) * 100;
        $revenueGrowth = $this->formatPercentage($revenueGrowth);
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

    public function revenue($filter){
        $startDate = null;
        $endDate = null;
        switch ($filter) {
            case 'allTime':
                $selectDate = DB::raw('MONTHNAME(created_at) as date');
                break;
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::now()->endOfDay();
                $selectDate = DB::raw('DAYNAME(created_at) as date');
                break;
            case 'weekly':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                $selectDate = DB::raw('DAYNAME(created_at) as date');
                break;
            case 'monthly':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                $selectDate = DB::raw('MONTHNAME(created_at) as date');
                break;
            case 'yearly':
//                $startDate = Carbon::now()->startOfYear();
//                $endDate = Carbon::now()->endOfYear();
                $selectDate = DB::raw('YEAR(created_at) as date');
                break;
        }


        $results = $this->invoiceRepository->revenue($selectDate, $startDate, $endDate);

        $totalPaidInvoices = $this->invoiceRepository->calculateInvoiceByStatus('Paid', $startDate, $endDate, countInvoice: true);
        $totalPaidInvoices = $this->formatNumber($totalPaidInvoices);

        $totalOpenInvoices = $this->invoiceRepository->calculateInvoiceByStatus('Open', $startDate, $endDate, countInvoice: true);
        $totalOpenInvoices = $this->formatNumber($totalOpenInvoices);

        $totalPastDueInvoices = $this->invoiceRepository->calculateInvoiceByStatus('Past Due', $startDate, $endDate, countInvoice: true);
        $totalPastDueInvoices = $this->formatNumber($totalPastDueInvoices);

        $totalUncollectInvoices = $this->invoiceRepository->calculateInvoiceByStatus('Uncollectible', $startDate, $endDate, countInvoice: true);
        $totalUncollectInvoices = $this->formatNumber($totalUncollectInvoices);

        $revenue = $this->invoiceRepository->calculateInvoiceByStatus('Paid', $startDate, $endDate, sumAmount: true);
        $sumPaidInvoices = $this->formatNumber($revenue);
        $totalRevenue = $this->formatReadableNumber($revenue);
        $sumOpenInvoices = $this->invoiceRepository->calculateInvoiceByStatus('Open', $startDate, $endDate, sumAmount: true);
        $sumOpenInvoices = $this->formatNumber($sumOpenInvoices);
        $sumPastDueInvoices = $this->invoiceRepository->calculateInvoiceByStatus('Past Due', $startDate, $endDate, sumAmount: true);
        $sumPastDueInvoices = $this->formatNumber($sumPastDueInvoices);
        $sumUncollectInvoices = $this->invoiceRepository->calculateInvoiceByStatus('Uncollectible', $startDate, $endDate, sumAmount: true);
        $sumUncollectInvoices = $this->formatNumber($sumUncollectInvoices);

//        $label = [];
//        $data = [];
//        foreach ($results as $result){
//            $label[] = $result->month_revenue;
//            $data[] = $result->total_ammount;
//        }
//        return compact('label', 'data');
        $chart = [
            'labels' => $results->pluck('date'),
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $results->pluck('total_ammount'),
                    'borderColor' => '#20F4CB',
                    'backgroundColor'=> 'rgba(32, 244, 203, 0.2)',
                    'fill' => 'start',
                    'tension' => 0.4,

                ],
            ],
        ];

        return compact(
            'chart',
            'totalPaidInvoices',
            'totalOpenInvoices',
            'totalPastDueInvoices',
            'totalUncollectInvoices',
            'sumPaidInvoices',
            'sumOpenInvoices',
            'sumPastDueInvoices',
            'sumUncollectInvoices',
            'totalRevenue'
        );
    }

    public function upcomingMatches(){
        return EventSchedule::with('teams')
            ->where('status', '1')
            ->where('eventType', 'Match')
            ->orderBy('date')
            ->take(5)
            ->get();

    }

    public function upcomingTrainings(){
        return EventSchedule::with('teams')
            ->where('status', '1')
            ->where('eventType', 'Training')
            ->orderBy('date')
            ->take(4)
            ->get();

    }


}
