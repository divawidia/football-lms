<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductCategoryRequest;
use App\Http\Requests\TaxRequest;
use App\Models\ProductCategory;
use App\Models\Tax;
use App\Services\TaxService;
use Illuminate\Support\Facades\Auth;

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
        $admin = Auth::user()->roles()->first();

        return response()->json($this->taxService->store($data, $admin->id));
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
        return response()->json($this->taxService->activate($tax));
    }

    public function deactivate(Tax $tax){
        return response()->json($this->taxService->deactivate($tax));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tax $tax)
    {
        return response()->json($this->taxService->destroy($tax));
    }
}
