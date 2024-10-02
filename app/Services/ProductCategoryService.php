<?php

namespace App\Services;

use App\Models\ProductCategory;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ProductCategoryService extends Service
{
    public function index()
    {
        $data = ProductCategory::all();
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                if ($item->status == '1') {
                    $statusButton = '<form action="' . route('product-categories.deactivate', $item->id) . '" method="POST">
                                        ' . method_field("PATCH") . '
                                        ' . csrf_field() . '
                                        <button type="submit" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="tooltip" data-placement="bottom" title="Deactivate Product Category">
                                            <span class="material-icons">block</span>
                                        </button>
                                    </form>';
                } else {
                    $statusButton = '<form action="' . route('product-categories.activate', $item->id) . '" method="POST">
                                        ' . method_field("PATCH") . '
                                        ' . csrf_field() . '
                                        <button type="submit" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="tooltip" data-placement="bottom" title="Activate Product Category">
                                            <span class="material-icons">check_circle</span>
                                        </button>
                                    </form>';
                }
                return '<div class="btn-toolbar" role="toolbar">
                            <button class="btn btn-sm btn-outline-secondary mr-1 editProductCategory" id="' . $item->id . '" type="button" data-toggle="tooltip" data-placement="bottom" title="Edit Product Category">
                                <span class="material-icons">edit</span>
                             </button>
                             ' . $statusButton . '
                            <button type="button" class="btn btn-sm btn-outline-secondary deleteProductCategory" id="' . $item->id . '" data-toggle="tooltip" data-placement="bottom" title="Edit Product Category">
                                <span class="material-icons">delete</span>
                            </button>
                        </div>';
            })
            ->editColumn('createdBy', function ($item) {
                return '
                            <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->admin->user->foto) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->admin->user->firstName . ' ' . $item->admin->user->lastName . '</strong></p>
                                            <small class="js-lists-values-email text-50">' . $item->admin->position->name . '</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            })
            ->editColumn('updatedAt', function ($item) {
                return $this->convertTimestamp($item->created_at);
            })
            ->editColumn('createdAt', function ($item) {
                return $this->convertTimestamp($item->updated_at);
            })
            ->editColumn('status', function ($item) {
                if ($item->status == '1') {
                    $badge = '<span class="badge badge-pill badge-success">Active</span>';
                } elseif ($item->status == '0') {
                    $badge = '<span class="badge badge-pill badge-danger">Non Active</span>';
                }
                return $badge;
            })
            ->rawColumns(['action', 'createdBy', 'updatedAt', 'createdAt', 'status'])
            ->addIndexColumn()
            ->make();
    }

    public function store(array $data, $adminId)
    {
        $data['adminId'] = $adminId;
        return ProductCategory::create($data);
    }

    public function update(array $data, ProductCategory $productCategory)
    {
        return $productCategory->update($data);
    }

    public function activate(ProductCategory $productCategory)
    {
        return $productCategory->update(['status' => '1']);
    }

    public function deactivate(ProductCategory $productCategory)
    {
        return $productCategory->update(['status' => '0']);
    }

    public function destroy(ProductCategory $productCategory)
    {
        return $productCategory->delete();
    }
}
