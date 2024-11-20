<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Services\AttendanceReportService;
use App\Services\FinancialReportService;
use Illuminate\Http\Request;

class FinancialReportController extends Controller
{
    private FinancialReportService $financialReportService;

    public function __construct(FinancialReportService $financialReportService)
    {
        $this->financialReportService = $financialReportService;
    }
    public function index(){
        $requireActionInvoice = $this->financialReportService->requireActionInvoice();
        $recurringRevenue = $this->financialReportService->estimatedRecuringRevenue();
        $revenueGrowth = $this->financialReportService->revenueGrowth();

        return view('pages.admins.academies.reports.financial.index', [
            'requireActionInvoice' => $requireActionInvoice,
            'recurringRevenue' => $recurringRevenue,
            'revenueGrowth' => $revenueGrowth
        ]);
    }
    public function revenueChartData(Request $request)
    {
        $filter = $request->input('filter');

        $data = $this->financialReportService->revenue($filter);
        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => 'Successfully retrieve revenue data',
            'data' => $data,
        ]);
    }
}
