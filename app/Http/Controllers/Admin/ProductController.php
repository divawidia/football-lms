<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductCategoryRequest;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\ProductCategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

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

        return view('pages.admins.payments.products.index', [
            'categories' => $this->productCategoryService->getAllData()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        return response()->json($this->productService->store($data, Auth::user()->id));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return response()->json([
            'status' => '200',
            'data' => $product,
            'message' => 'Success'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();

        return response()->json($this->productService->update($data, $product));
    }

    public function activate(Product $product){
        $this->productService->activate($product);
        $text = 'Product '. $product->productName . ' status successfully activated!';
        Alert::success($text);
        return redirect()->route('products.index');
    }

    public function deactivate(Product $product){
        $this->productService->deactivate($product);
        $text = 'Product '. $product->productName . ' status successfully deactivated!';
        Alert::success($text);
        return redirect()->route('products.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        return response()->json($this->productService->destroy($product));
    }
}
