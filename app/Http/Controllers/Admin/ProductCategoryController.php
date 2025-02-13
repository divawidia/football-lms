<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductCategoryRequest;
use App\Models\ProductCategory;
use App\Services\ProductCategoryService;
use Illuminate\Http\JsonResponse;

class ProductCategoryController extends Controller
{
    private ProductCategoryService $productCategoryService;

    public function __construct(ProductCategoryService $productCategoryService)
    {
        $this->productCategoryService = $productCategoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return $this->productCategoryService->index();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        return ApiResponse::success($this->productCategoryService->store($data, $this->getLoggedUser()), "Product category successfully added.");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCategory $productCategory): JsonResponse
    {
        return ApiResponse::success($productCategory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductCategoryRequest $request, ProductCategory $productCategory): JsonResponse
    {
        $data = $request->validated();
        $this->productCategoryService->update($data, $productCategory);
        return ApiResponse::success(message: "Product category : {$productCategory->categoryName} updated successfully.");
    }

    public function activate(ProductCategory $productCategory): JsonResponse
    {
        $this->productCategoryService->activate($productCategory);
        return ApiResponse::success(message: "Product category : {$productCategory->categoryName} successfully activated.");
    }

    public function deactivate(ProductCategory $productCategory): JsonResponse
    {
        $this->productCategoryService->deactivate($productCategory);
        return ApiResponse::success(message: "Product category : {$productCategory->categoryName} successfully deactivated.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $productCategory): JsonResponse
    {
        $this->productCategoryService->destroy($productCategory);
        return ApiResponse::success(message: "Product category : {$productCategory->categoryName} successfully deleted.");
    }
}
