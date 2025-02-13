<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\Product;
use App\Repository\ProductRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class ProductService extends Service
{
    public ProductRepository $productRepository;
    private DatatablesHelper $datatablesHelper;

    public function __construct(ProductRepository $productRepository, DatatablesHelper $datatablesHelper){
        $this->productRepository = $productRepository;
        $this->datatablesHelper = $datatablesHelper;
    }

    public function getAllProducts($withRelations = [], $priceOption = null, $status = null): Collection
    {
        return $this->productRepository->getAll($withRelations, $priceOption, $status);
    }

    public function index(): JsonResponse
    {
        $data = $this->getAllProducts(['user', 'category']);
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                $dropdownItem = $this->datatablesHelper->buttonDropdownItem('editProduct', $item->hash, icon: 'edit', btnText: 'Edit Product');
                ($item->status == '1')
                    ? $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('setDeactivateProduct', $item->hash, 'danger', icon: 'check_circle', btnText: 'Deactivate product')
                    : $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('setActivateProduct', $item->hash, 'success', icon: 'check_circle', btnText: 'Activate product');
                $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('deleteProduct', $item->hash, 'danger', icon: 'delete', btnText: 'Delete Product');
                return $this->datatablesHelper->dropdown(function () use ($dropdownItem) {
                    return $dropdownItem;
                });
            })
            ->editColumn('createdBy', function ($item) {
                return $this->datatablesHelper->name($item->user->foto, $this->getUserFullName($item->user), $item->position, route('admin-managements.show',  $item->user->admin->hash));
            })
            ->editColumn('description', function ($item) {
                return $this->description($item->description);
            })
            ->editColumn('updatedAt', function ($item) {
                return $this->convertToDatetime($item->created_at);
            })
            ->editColumn('createdAt', function ($item) {
                return $this->convertToDatetime($item->updated_at);
            })
            ->editColumn('price', function ($item) {
                return $this->priceFormat($item->price);
            })
            ->editColumn('status', function ($item) {
                return $this->datatablesHelper->activeNonactiveStatus($item->status);
            })
            ->editColumn('subscriptionCycle', function ($item) {
                return ($item->priceOption == 'subscription') ? '<p class="text-capitalize">'.$item->subscriptionCycle.'</p>' : 'Not Subscription';
            })
            ->rawColumns(['action', 'createdBy', 'description', 'status', 'subscriptionCycle'])
            ->addIndexColumn()
            ->make();
    }

    public function store(array $data, $loggedUser): JsonResponse
    {
        $data['userId'] = $loggedUser->id;
        return $this->productRepository->create($data);
    }

    public function update(array $data, Product $product): bool
    {
        return $this->productRepository->update($data, $product);
    }

    public function activate(Product $product): bool
    {
        return $product->update(['status' => '1']);
    }

    public function deactivate(Product $product): bool
    {
        return $product->update(['status' => '0']);
    }

    public function destroy(Product $product): ?bool
    {
        return $this->productRepository->delete($product);
    }
}
