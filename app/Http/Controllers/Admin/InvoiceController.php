<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Models\Product;
use App\Models\Tax;
use App\Models\User;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
