<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductCategoryRequest;
use App\Models\ProductCategory;
use App\Services\ProductCategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    public function index()
    {
        return $this->productCategoryService->index();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductCategoryRequest $request)
    {
        $data = $request->validated();
        $admin = Auth::user()->roles()->first();

        return response()->json($this->productCategoryService->store($data, $admin->id));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCategory $productCategory)
    {
        return response()->json([
            'status' => '200',
            'data' => $productCategory,
            'message' => 'Success'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductCategoryRequest $request, ProductCategory $productCategory)
    {
        $data = $request->validated();

        return response()->json($this->productCategoryService->update($data, $productCategory));
    }

    public function activate(ProductCategory $productCategory){
        return response()->json($this->productCategoryService->activate($productCategory));
    }

    public function deactivate(ProductCategory $productCategory){
        return response()->json($this->productCategoryService->deactivate($productCategory));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $productCategory)
    {
        return response()->json($this->productCategoryService->destroy($productCategory));
    }
}
