<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Tax;
use App\Models\Team;
use App\Models\User;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class InvoiceController extends Controller
{
    private InvoiceService $invoiceService;
    private Product $product;
    private Tax $tax;
    private User $user;

    public function __construct(InvoiceService $invoiceService, Product $product, Tax $tax, User $user){
        $this->invoiceService = $invoiceService;
        $this->product = $product;
        $this->tax = $tax;
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\request()->ajax()){
            return $this->invoiceService->index();
        }

        return view('pages.admins.payments.invoices.index', [
            'products' => $this->product->getAllProducts(),
            'taxes' => $this->tax->getAllTax(),
            'contacts' => $this->user->getAllUserWithoutLoggedUserData($this->getLoggedUserId())
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InvoiceRequest $request)
    {
        $data = $request->validated();
        $loggedUserId = $this->getLoggedUserId();
        $academyId = $this->getAcademyId();
        $result = $this->invoiceService->store($data, $loggedUserId, $academyId);
        return response()->json([
            'status' => 200,
            'data' => $result,
            'message'=> 'Success'
        ]);
    }

    public function calculateProductAmount(Request $request){
        $qty = $request->query('qty');
        $productId = $request->query('productId');

        $data = $this->invoiceService->calculateProductAmount($qty, $productId);

        return response()->json([
            'status' => 200,
            'data' => $data,
            'message' => 'Success'
        ]);
    }

    public function calculateInvoiceTotal(Request $request){
        $data = $request->all();

        $result = $this->invoiceService->calculateInvoiceTotal($data);

        return response()->json([
            'status' => 200,
            'data' => $data,
            'message' => 'Success'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        return view('pages.admins.payments.invoices.detail', [
            'data' => $this->invoiceService->show($invoice)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        return view('pages.admins.payments.invoices.edit', [
            'data' => $invoice,
            'products' => $this->product->getAllProducts(),
            'taxes' => $this->tax->getAllTax(),
            'contacts' => $this->user->getAllUserWithoutLoggedUserData($this->getLoggedUserId())
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InvoiceRequest $request, Invoice $invoice)
    {
        $data = $request->validated();
        $this->invoiceService->update($data, $invoice);

        $text = 'Invoice '.$invoice->invoiceNumber.' successfully updated';
        Alert::success($text);
        return redirect()->route('invoices.show', ['invoice'=>$invoice->id]);
    }

    public function setPaid(Invoice $invoice){
        $this->invoiceService->paid($invoice);

        $text = 'Invoice '.$invoice->invoiceNumber.' status successfully mark as paid';
        Alert::success($text);
        return redirect()->route('invoices.show', $invoice->id);
    }

    public function setUncollectible(Invoice $invoice){
        $this->invoiceService->uncollectible($invoice);

        $text = 'Invoice '.$invoice->invoiceNumber.' status successfully mark as uncollectible';
        Alert::success($text);
        return redirect()->route('invoices.show', $invoice->id);
    }

    public function setOpen(Invoice $invoice){
        $this->invoiceService->open($invoice);

        $text = 'Invoice '.$invoice->invoiceNumber.' status successfully mark as open';
        Alert::success($text);
        return redirect()->route('invoices.show', $invoice->id);
    }

    public function setPastDue(Invoice $invoice){
        $this->invoiceService->pastDue($invoice);

        $text = 'Invoice '.$invoice->invoiceNumber.' status successfully mark as past due';
        Alert::success($text);
        return redirect()->route('invoices.show', $invoice->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
