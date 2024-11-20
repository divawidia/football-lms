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
use App\Repository\SubscriptionRepository;
use Illuminate\Support\Number;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialReportService extends Service
{
    private InvoiceRepository $invoiceRepository;
    private SubscriptionRepository $subscriptionRepository;
    public function __construct(InvoiceRepository $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    public function requireActionInvoice()
    {
        $pastDueInvoices = $this->invoiceRepository->calculateInvoiceByStatus('Past Due', countInvoice: true);
        $uncollectInvoices = $this->invoiceRepository->calculateInvoiceByStatus('Uncollectible', countInvoice: true);
        $totalRequireActionInvoice = $pastDueInvoices +$uncollectInvoices;

        $sumPastDueInvoices = $this->invoiceRepository->calculateInvoiceByStatus('Past Due',sumAmount: true);
        $sumUncollectInvoices = $this->invoiceRepository->calculateInvoiceByStatus('Uncollectible', sumAmount: true);
        $sumRequireActionInvoice = $sumPastDueInvoices + $sumUncollectInvoices;

        $totalPastDueInvoices = $this->formatNumber($pastDueInvoices);
        $totalUncollectInvoices = $this->formatNumber($uncollectInvoices);
        $totalRequireActionInvoice = $this->formatNumber($totalRequireActionInvoice);
        $sumPastDueInvoices = $this->formatNumber($sumPastDueInvoices);
        $sumUncollectInvoices = $this->formatNumber($sumUncollectInvoices);
        $sumRequireActionInvoice = $this->formatNumber($sumRequireActionInvoice);

        return compact(
            'totalPastDueInvoices',
            'totalUncollectInvoices',
            'totalRequireActionInvoice',
            'sumPastDueInvoices',
            'sumUncollectInvoices',
            'sumRequireActionInvoice',
            );
    }

    public function estimatedRecuringRevenue()
    {
        $result = $this->subscriptionRepository->recurringRevenue();

        return [
            'yrr' => $result->yrr,
            'qrr' => $result->qrr,
            'mrr' => $result->mrr,
        ];
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
}
