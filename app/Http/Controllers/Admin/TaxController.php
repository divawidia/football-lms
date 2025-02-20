<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaxRequest;
use App\Models\Tax;
use App\Services\TaxService;
use Illuminate\Http\JsonResponse;

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
    public function index(): JsonResponse
    {
        return $this->taxService->index();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaxRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->taxService->store($data, $this->getLoggedUser());
        return ApiResponse::success(message: "Tax successfully added!");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tax $tax): JsonResponse
    {
        return ApiResponse::success($tax);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaxRequest $request, Tax $tax): JsonResponse
    {
        $data = $request->validated();
        $this->taxService->update($data, $tax);
        return ApiResponse::success(message: "Tax : {$tax->taxName} successfully updated!");
    }

    public function activate(Tax $tax): JsonResponse
    {
        $this->taxService->activate($tax);
        return ApiResponse::success(message: "Tax : {$tax->taxName} successfully activated!");
    }

    public function deactivate(Tax $tax): JsonResponse
    {
        $this->taxService->deactivate($tax);
        return ApiResponse::success(message: "Tax : {$tax->taxName} successfully deactivated!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tax $tax): JsonResponse
    {
        $this->taxService->destroy($tax);
        return ApiResponse::success(message: "Tax : {$tax->taxName} successfully deleted!");
    }
}
