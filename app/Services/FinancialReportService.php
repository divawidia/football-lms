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
    public function __construct(InvoiceRepository $invoiceRepository, SubscriptionRepository $subscriptionRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
        $this->subscriptionRepository = $subscriptionRepository;
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

    public function revenueGrowth()
    {
        $startMonth = Carbon::now()->startOfMonth();
        $now = Carbon::now();
        $startOfLastMonth = now()->subMonth()->startOfMonth();
        $endOfLastMonth = now()->subMonth()->endOfMonth();

        $thisMonthTotalRevenues = $this->invoiceRepository->calculateInvoiceByStatus('Paid', $startMonth, $now, sumAmount: true);
        $previousMonthTotalRevenues = $this->invoiceRepository->calculateInvoiceByStatus('Paid', $startOfLastMonth, $endOfLastMonth, sumAmount: true);
        $revenueGrowth = (($thisMonthTotalRevenues - $previousMonthTotalRevenues) / $previousMonthTotalRevenues) * 100;
        return $this->formatPercentage($revenueGrowth);
    }

    public function estimatedRecuringRevenue()
    {
        $result = $this->subscriptionRepository->recurringRevenue();

        return [
            'yrr' => $this->formatNumber($result->yrr),
            'qrr' => $this->formatNumber($result->qrr),
            'mrr' => $this->formatNumber($result->mrr),
        ];
    }

    public function invoiceStatus(){
        $results = $this->invoiceRepository->invoiceStatus();

        $label = $results->pluck('status');
        $data = $results->pluck('count');

        return compact('label', 'data');
    }

    public function paymentType(){
        $results = $this->invoiceRepository->paymentType();

        $label = $results->pluck('paymentMethod');
        $data = $results->pluck('count');

        return compact('label', 'data');
    }

    public function filter($filter)
    {
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
        return compact('startDate', 'endDate', 'selectDate');
    }
    public function revenue($filter)
    {
        $queryFilter = $this->filter($filter);
        $startDate = $queryFilter['startDate'];
        $endDate = $queryFilter['endDate'];
        $selectDate = $queryFilter['selectDate'];

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

    public function playerSubscription($filter)
    {
        $queryFilter = $this->filter($filter);
        $startDate = $queryFilter['startDate'];
        $endDate = $queryFilter['endDate'];
        $selectDate = $queryFilter['selectDate'];

        $results = $this->subscriptionRepository->playerSubscriptionTrend('Scheduled', $selectDate,$startDate, $endDate);
        $totalScheduled = $this->subscriptionRepository->countSubscriptionByStatus('Scheduled',$startDate, $endDate);
        $totalUnsubscribed = $this->subscriptionRepository->countSubscriptionByStatus('Unsubscribed',$startDate, $endDate);
        $totalPending = $this->subscriptionRepository->countSubscriptionByStatus('Pending Payment',$startDate, $endDate);
        $totalSubsbcription = $totalScheduled + $totalUnsubscribed + $totalPending;
        
        $chart = [
            'labels' => $results->pluck('date'),
            'datasets' => [
                [
                    'label' => '# of Players',
                    'data' => $results->pluck('count'),
                    'borderColor' => '#E52534',
                    'backgroundColor'=> 'rgba(229, 37, 52, 0.2)',
                    'fill' => 'start',
                    'tension' => 0.4,

                ],
            ],
        ];
        
        return compact('totalPending', 'totalUnsubscribed', 'totalScheduled', 'totalSubsbcription', 'chart');
    }
}
