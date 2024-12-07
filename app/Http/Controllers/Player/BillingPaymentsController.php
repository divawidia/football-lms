<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Tax;
use App\Models\Team;
use App\Models\User;
use App\Services\DatatablesService;
use App\Services\InvoiceService;
use App\Services\Player\BillingPaymentsService;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class BillingPaymentsController extends Controller
{
    private BillingPaymentsService $billingPaymentsService;
    private DatatablesService $datatablesService;

    public function __construct(BillingPaymentsService $billingPaymentsService, DatatablesService $datatablesService){
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
        $openInvoices = $this->billingPaymentsService->openInvoices();
        return view('pages.payments.billing-payments.index', ['openInvoices' => $openInvoices]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        return view('pages.payments.billing-payments.detail', [
            'data' => $this->billingPaymentsService->show($invoice)
        ]);
    }
}
