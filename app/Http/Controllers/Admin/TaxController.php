<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductCategoryRequest;
use App\Http\Requests\TaxRequest;
use App\Models\ProductCategory;
use App\Models\Tax;
use App\Services\TaxService;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class TaxController extends Controller
{
    private TaxService $taxService;

    public function __construct(TaxService $taxService)
    {
        $this->taxService = $taxService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->taxService->index();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaxRequest $request)
    {
        $data = $request->validated();
        $admin = Auth::user()->id;

        return response()->json($this->taxService->store($data, $admin));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tax $tax)
    {
        return response()->json([
            'status' => '200',
            'data' => $tax,
            'message' => 'Success'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaxRequest $request, Tax $tax)
    {
        $data = $request->validated();

        return response()->json($this->taxService->update($data, $tax));
    }

    public function activate(Tax $tax){
        $this->taxService->activate($tax);
        $text = 'Tax '. $tax->taxName . ' status successfully activated!';
        Alert::success($text);
        return redirect()->route('products.index');
    }

    public function deactivate(Tax $tax){
        $this->taxService->activate($tax);
        $text = 'Tax '. $tax->taxName . ' status successfully activated!';
        Alert::success($text);
        return redirect()->route('products.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tax $tax)
    {
        return response()->json($this->taxService->destroy($tax));
    }
}
