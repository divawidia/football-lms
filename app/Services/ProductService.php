<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ProductService extends Service
{
    private Product $product;

    public function __construct(Product $product){
        $this->product = $product;
    }

    public function index()
    {
        $data = $this->product->getAllProducts();
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                if ($item->status == '1') {
                    $statusButton = '<form action="' . route('products.deactivate', $item->id) . '" method="POST">
                                        ' . method_field("PATCH") . '
                                        ' . csrf_field() . '
                                        <button type="submit" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="tooltip" data-placement="bottom" title="Deactivate Product">
                                            <span class="material-icons">block</span>
                                        </button>
                                    </form>';
                } else {
                    $statusButton = '<form action="' . route('products.activate', $item->id) . '" method="POST">
                                        ' . method_field("PATCH") . '
                                        ' . csrf_field() . '
                                        <button type="submit" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="tooltip" data-placement="bottom" title="Activate Product">
                                            <span class="material-icons">check_circle</span>
                                        </button>
                                    </form>';
                }
                return '<div class="btn-toolbar" role="toolbar">
                            <button class="btn btn-sm btn-outline-secondary mr-1 edit-product" id="' . $item->id . '" type="button" data-toggle="tooltip" data-placement="bottom" title="Edit Product">
                                <span class="material-icons">edit</span>
                             </button>
                             ' . $statusButton . '
                            <button type="button" class="btn btn-sm btn-outline-secondary delete-product" id="' . $item->id . '" data-toggle="tooltip" data-placement="bottom" title="Delete Product">
                                <span class="material-icons">delete</span>
                            </button>
                        </div>';
            })
            ->editColumn('createdBy', function ($item) {
                return '
                            <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->user->foto) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->user->firstName . ' ' . $item->user->lastName . '</strong></p>
                                            <small class="js-lists-values-email text-50">' . $item->user->admin->position . '</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
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
                if ($item->status == '1') {
                    $badge = '<span class="badge badge-pill badge-success">Active</span>';
                } elseif ($item->status == '0') {
                    $badge = '<span class="badge badge-pill badge-danger">Non Active</span>';
                }
                return $badge;
            })
            ->editColumn('subscriptionCycle', function ($item) {
                if ($item->priceOption == 'subscription') {
                    $badge = '<p class="text-capitalize">'.$item->subscriptionCycle.'</p>';
                } elseif ($item->priceOption == 'one time payment') {
                    $badge = 'Not Subscription';
                }
                return $badge;
            })
            ->rawColumns(['action', 'createdBy', 'description', 'updatedAt', 'createdAt', 'price', 'status', 'subscriptionCycle'])
            ->addIndexColumn()
            ->make();
    }

    public function store(array $data, $adminId)
    {
        $data['userId'] = $adminId;
        return Product::create($data);
    }

    public function update(array $data, Product $product)
    {
        return $product->update($data);
    }

    public function activate(Product $product)
    {
        return $product->update(['status' => '1']);
    }

    public function deactivate(Product $product)
    {
        return $product->update(['status' => '0']);
    }

    public function destroy(Product $product)
    {
        return $product->delete();
    }
}
