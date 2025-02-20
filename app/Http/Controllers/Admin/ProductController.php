<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Services\ProductCategoryService;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    private ProductService $productService;
    private ProductCategoryService $productCategoryService;

    public function __construct(ProductService $productService, ProductCategoryService $productCategoryService)
    {
        $this->productService = $productService;
        $this->productCategoryService = $productCategoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\request()->ajax()){
            return $this->productService->index();
        }

        return view('pages.payments.products.index', [
            'categories' => $this->productCategoryService->getAllData('1')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request): JsonResponse
    {
        $data = $request->validated();
        return ApiResponse::success($this->productService->store($data, $this->getLoggedUser()), "Product successfully added!");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): JsonResponse
    {
        return ApiResponse::success($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        $data = $request->validated();
        $this->productService->update($data, $product);
        return ApiResponse::success(message: "Product : {$product->productName} successfully updated!");
    }

    public function activate(Product $product): JsonResponse
    {
        $this->productService->activate($product);
        return ApiResponse::success(message: "Product : {$product->productName} successfully activated!");
    }

    public function deactivate(Product $product): JsonResponse
    {
        $this->productService->deactivate($product);
        return ApiResponse::success(message: "Product : {$product->productName} successfully deactivated!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->productService->destroy($product);
        return ApiResponse::success(message: "Product : {$product->productName} successfully deleted!");
    }
}
