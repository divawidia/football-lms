<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Tax;
use App\Models\Team;
use App\Models\User;
use App\Services\InvoiceService;
use App\Services\Player\BillingPaymentsService;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class BillingPaymentsController extends Controller
{
    private BillingPaymentsService $billingPaymentsService;

    public function __construct(BillingPaymentsService $billingPaymentsService){
        $this->middleware(function ($request, $next) {
            $this->billingPaymentsService = new BillingPaymentsService($this->getLoggedPLayerUser()->user);
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
        return view('pages.players.billing-payments.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        return view('pages.players.billing-payments.detail', [
            'data' => $this->billingPaymentsService->show($invoice)
        ]);
    }
}
