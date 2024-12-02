<?php

namespace App\Services;

use App\Models\ProductCategory;
use App\Models\Tax;
use App\Repository\TaxRepository;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class TaxService extends Service
{
    private TaxRepository $taxRepository;

    public function __construct(TaxRepository $taxRepository){
        $this->taxRepository = $taxRepository;
    }
    public function index()
    {
        $data = $this->taxRepository->getAll();
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                if ($item->status == '1') {
                    $statusButton = '<form action="' . route('taxes.deactivate', $item->id) . '" method="POST">
                                        ' . method_field("PATCH") . '
                                        ' . csrf_field() . '
                                        <button type="submit" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="tooltip" data-placement="bottom" title="Deactivate Tax">
                                            <span class="material-icons">block</span>
                                        </button>
                                    </form>';
                } else {
                    $statusButton = '<form action="' . route('taxes.activate', $item->id) . '" method="POST">
                                        ' . method_field("PATCH") . '
                                        ' . csrf_field() . '
                                        <button type="submit" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="tooltip" data-placement="bottom" title="Activate Tax">
                                            <span class="material-icons">check_circle</span>
                                        </button>
                                    </form>';
                }
                return '<div class="btn-toolbar" role="toolbar">
                            <button class="btn btn-sm btn-outline-secondary mr-1 editTax" id="' . $item->id . '" type="button" data-toggle="tooltip" data-placement="bottom" title="Edit Tax">
                                <span class="material-icons">edit</span>
                             </button>
                             ' . $statusButton . '
                            <button type="button" class="btn btn-sm btn-outline-secondary deleteTax" id="' . $item->id . '" data-toggle="tooltip" data-placement="bottom" title="Edit Tax">
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

    public function store(array $data, $userId)
    {
        $data['userId'] = $userId;
        return Tax::create($data);
    }

    public function update(array $data, Tax $tax)
    {
        return $tax->update($data);
    }

    public function activate(Tax $tax)
    {
        return $tax->update(['status' => '1']);
    }

    public function deactivate(Tax $tax)
    {
        return $tax->update(['status' => '0']);
    }

    public function destroy(Tax $tax)
    {
        return $tax->delete();
    }
}
