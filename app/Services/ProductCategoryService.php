<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class ProductCategoryService extends Service
{
    private ProductCategory $productCategory;
    private DatatablesHelper $datatablesHelper;

    public function __construct(ProductCategory $productCategory, DatatablesHelper $datatablesHelper){
        $this->productCategory = $productCategory;
        $this->datatablesHelper = $datatablesHelper;
    }
    public function getAllData($status = null, $withRelation = []): Collection
    {
        $query = $this->productCategory->with($withRelation);
        if ($status != null) {
            $query->where('status', $status);
        }
        return $query->get();
    }
    public function index(): JsonResponse
    {
        return Datatables::of($this->getAllData())
            ->addColumn('action', function ($item) {
                $dropdownItem = $this->datatablesHelper->buttonDropdownItem('editProductCategory', $item->hash, icon: 'edit', btnText: 'Edit Product category');
                ($item->status == '1')
                    ? $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('setDeactivateProductCategory', $item->hash, 'danger', icon: 'check_circle', btnText: 'Deactivate product category')
                    : $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('setActivateProductCategory', $item->hash, 'success', icon: 'check_circle', btnText: 'Activate product category');
                $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('deleteProductCategory', $item->hash, 'danger', icon: 'delete', btnText: 'Delete Product category');
                return $this->datatablesHelper->dropdown(function () use ($dropdownItem) {
                    return $dropdownItem;
                });
            })
            ->editColumn('createdBy', function ($item) {
                return $this->datatablesHelper->name($item->user->foto, $this->getUserFullName($item->user), $item->position, route('admin-managements.show', $item->user->admin->hash));
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
            ->editColumn('status', function ($item) {
                return $this->datatablesHelper->activeNonactiveStatus($item->status);
            })
            ->rawColumns(['action', 'createdBy', 'description', 'status'])
            ->addIndexColumn()
            ->make();
    }

    public function store(array $data, $loggedUser)
    {
        $data['userId'] = $loggedUser->id;
        return $this->productCategory->create($data);
    }

    public function update(array $data, ProductCategory $productCategory): bool
    {
        return $productCategory->update($data);
    }

    public function activate(ProductCategory $productCategory): bool
    {
        return $productCategory->update(['status' => '1']);
    }

    public function deactivate(ProductCategory $productCategory): bool
    {
        return $productCategory->update(['status' => '0']);
    }

    public function destroy(ProductCategory $productCategory): ?bool
    {
        return $productCategory->delete();
    }
}
