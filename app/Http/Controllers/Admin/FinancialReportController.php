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

        return view('pages.admins.academies.reports.financial.index', [
            'requireActionInvoice' => $requireActionInvoice,
            'recurringRevenue' => $recurringRevenue
        ]);
    }
}
