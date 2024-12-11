<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class InvoiceController extends Controller
{
    private InvoiceService $invoiceService;
    public function __construct(InvoiceService $invoiceService){
        $this->invoiceService = $invoiceService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\request()->ajax()){
            return $this->invoiceService->index();
        }

        return view('pages.payments.invoices.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = $this->invoiceService->invoiceForms();
        return view('pages.payments.invoices.create', [
            'products' => $data['products'],
            'taxes' => $data['taxes'],
            'contacts' => $data['players'],
        ]);
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

        $text = 'Invoice '.$result->invoiceNumber.' successfully created';
        Alert::success($text);
        return redirect()->route('invoices.show', $result->id);
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
        return view('pages.payments.invoices.detail', [
            'data' => $this->invoiceService->show($invoice)
        ]);
    }

    public function showArchived(string $id)
    {
        $data = $this->invoiceService->showArchived($id);
        return view('pages.payments.invoices.detail-archived', [
            'data' => $data
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        $data = $this->invoiceService->invoiceForms();
        return view('pages.payments.invoices.edit', [
            'data' => $invoice,
            'products' => $data['products'],
            'taxes' => $data['taxes'],
            'contacts' => $data['players'],
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
        try {
            $this->invoiceService->paid($invoice);
            return ApiResponse::success(message:  'Invoice '.$invoice->invoiceNumber.' status successfully mark as paid!');

        } catch (Exception $e) {
            Log::error('Error marking invoice as paid: ' . $e->getMessage());
            return ApiResponse::error(message: 'An error occurred while marking the invoice as paid.', status: $e->getCode());
        }
    }

    public function setUncollectible(Invoice $invoice){
        try {
            $this->invoiceService->uncollectible($invoice);
            return ApiResponse::success(message:  'Invoice '.$invoice->invoiceNumber.' status have been mark as uncollectible!');

        } catch (Exception $e) {
            Log::error('Error marking invoice as uncollectible: ' . $e->getMessage());
            return ApiResponse::error(message: 'An error occurred while marking the invoice as uncollectible.', status: $e->getCode());
        }
    }

    public function setOpen(Invoice $invoice){
        $loggedUser = auth()->user()->getAuthIdentifier();
        try {
            $this->invoiceService->open($invoice, $loggedUser);
            return ApiResponse::success(message:  'Invoice '.$invoice->invoiceNumber.' status successfully mark as open to pay!');

        } catch (Exception $e) {
            Log::error('Error marking invoice as open: ' . $e->getMessage());
            return ApiResponse::error(message: 'An error occurred while marking the open to pay.', status: $e->getCode());
        }
    }

    public function setPastDue(Invoice $invoice){
        try {
            $this->invoiceService->pastDue($invoice);
            return ApiResponse::success(message:  'Invoice '.$invoice->invoiceNumber.' status successfully mark as past due!');

        } catch (Exception $e) {
            Log::error('Error marking invoice as open: ' . $e->getMessage());
            return ApiResponse::error(message: 'An error occurred while marking the past due.', status: $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $result = $this->invoiceService->destroy($invoice);
        $message = "Invoice successfully archived.";
        return ApiResponse::success($result, $message);
    }

    public function deletedData()
    {
        if (\request()->ajax()){
            return $this->invoiceService->deletedDataIndex();
        }

        return view('pages.payments.invoices.archived');
    }

    public function restoreData(string $id)
    {
        $result = $this->invoiceService->restoreData($id);

        $message = "Invoice successfully restored.";
        return ApiResponse::success($result, $message);
    }

    public function permanentDeleteData(string $id)
    {
        $result = $this->invoiceService->permanentDeleteData($id);

        $message = "Invoice successfully permanently deleted.";
        return ApiResponse::success($result, $message);
    }
}
