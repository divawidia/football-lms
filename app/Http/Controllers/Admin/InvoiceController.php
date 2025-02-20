<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Services\InvoiceService;
use App\Services\ProductService;
use App\Services\TaxService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class InvoiceController extends Controller
{
    private InvoiceService $invoiceService;
    private ProductService $productService;
    private TaxService $taxService;
    private UserService $userService;
    public function __construct(
        InvoiceService $invoiceService,
        ProductService $productService,
        TaxService $taxService,
        UserService $userService,
    )
    {
        $this->productService = $productService;
        $this->taxService = $taxService;
        $this->userService = $userService;
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
        return view('pages.payments.invoices.create', [
            'contacts' => $this->userService->getAllUsers(role: 'player'),
            'taxes' => $this->taxService->getAllTaxes(status: '1'),
            'products' => $this->productService->getAllProducts(status: '1'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InvoiceRequest $request)
    {
        $data = $request->validated();

        if ($this->invoiceService->checkPlayerAlreadySubscribed($data)){
            return back()->with('error', 'Subscription product has been added to player, select another product except selected subscription product.');
        } else {
            $result = $this->invoiceService->store($data, $this->getLoggedUserId(), $this->getAcademyId());
            Alert::success("Invoice {$result->invoiceNumber} successfully created");
            return redirect()->route('invoices.show', $result->hash);
        }
    }

    public function calculateProductAmount(Request $request): JsonResponse
    {
        $qty = $request->query('qty');
        $productId = $request->query('productId');

        $data = $this->invoiceService->calculateProductAmount($qty, $productId);
        return ApiResponse::success($data);
    }

    public function calculateInvoiceTotal(Request $request): JsonResponse
    {
        $data = $request->all();
        $result = $this->invoiceService->calculateInvoiceTotal($data);
        return ApiResponse::success($result);
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        return view('pages.payments.invoices.detail', [
            'data' => $invoice
        ]);
    }

    public function showArchived(string $id)
    {
        return view('pages.payments.invoices.detail-archived', [
            'data' => $this->invoiceService->showArchived($id)
        ]);
    }

    public function setPaid(Invoice $invoice): JsonResponse
    {
        try {
            $this->invoiceService->paid($invoice);
            return ApiResponse::success(message:  'Invoice '.$invoice->invoiceNumber.' status has been successfully paid!');

        } catch (Exception $e) {
            Log::error('Error marking invoice as paid: ' . $e->getMessage());
            return ApiResponse::error(message: 'An error occurred while marking the invoice as paid.', status: $e->getCode());
        }
    }

    public function setUncollectible(Invoice $invoice): JsonResponse
    {
        try {
            $this->invoiceService->uncollectible($invoice);
            return ApiResponse::success(message:  'Invoice '.$invoice->invoiceNumber.' status has been marked as uncollectible!');

        } catch (Exception $e) {
            Log::error('Error marking invoice as uncollectible: ' . $e->getMessage());
            return ApiResponse::error(message: 'An error occurred while marking the invoice as uncollectible.', status: $e->getCode());
        }
    }

    public function setOpen(Invoice $invoice): JsonResponse
    {
        try {
            $this->invoiceService->open($invoice);
            return ApiResponse::success(message:  'Invoice '.$invoice->invoiceNumber.' status has been successfully marked as open to pay!');

        } catch (Exception $e) {
            Log::error('Error marking invoice as open: ' . $e->getMessage());
            return ApiResponse::error(message: 'An error occurred while marking the open to pay.', status: $e->getCode());
        }
    }

    public function setPastDue(Invoice $invoice): JsonResponse
    {
        try {
            $this->invoiceService->pastDue($invoice);
            return ApiResponse::success(message:  'Invoice '.$invoice->invoiceNumber.' status has been successfully marked as past due!');

        } catch (Exception $e) {
            Log::error('Error marking invoice as open: ' . $e->getMessage());
            return ApiResponse::error(message: 'An error occurred while marking the past due.', status: $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice): JsonResponse
    {
        $this->invoiceService->destroy($invoice);
        return ApiResponse::success(message: "Invoice has been successfully archived.");
    }

    public function deletedData()
    {
        if (\request()->ajax()){
            return $this->invoiceService->deletedDataIndex();
        }
        return view('pages.payments.invoices.archived');
    }

    public function restoreData(string $id): JsonResponse
    {
        $this->invoiceService->restoreData($id);
        return ApiResponse::success(message: "Invoice has been successfully restored.");
    }

    public function permanentDeleteData(string $id): JsonResponse
    {
        $this->invoiceService->permanentDeleteData($id);
        return ApiResponse::success(message: "Invoice has been successfully permanently deleted.");
    }
}
