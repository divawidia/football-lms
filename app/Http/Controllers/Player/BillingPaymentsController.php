<?php

namespace App\Http\Controllers\Player;

use App\Helpers\DatatablesHelper;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\Player\BillingPaymentsService;

class BillingPaymentsController extends Controller
{
    private BillingPaymentsService $billingPaymentsService;
    private DatatablesHelper $datatablesService;

    public function __construct(BillingPaymentsService $billingPaymentsService, DatatablesHelper $datatablesService){
        $this->middleware(function ($request, $next) use ($datatablesService) {
            $this->billingPaymentsService = new BillingPaymentsService($this->getLoggedPLayerUser()->user, $datatablesService);
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\request()->ajax()){
            return $this->billingPaymentsService->index();
        }
        return view('pages.payments.billing-payments.index', [
            'openInvoices' => $this->billingPaymentsService->openInvoices()
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        return view('pages.payments.billing-payments.detail', [
            'data' => $invoice
        ]);
    }
}
