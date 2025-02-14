<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\Tax;
use App\Repository\TaxRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class TaxService extends Service
{
    private TaxRepository $taxRepository;
    private DatatablesHelper $datatablesHelper;

    public function __construct(TaxRepository $taxRepository, DatatablesHelper $datatablesHelper)
    {
        $this->taxRepository = $taxRepository;
        $this->datatablesHelper = $datatablesHelper;
    }

    public function getAllTaxes($withRelations = [], $status = null): Collection
    {
        return $this->taxRepository->getAll($withRelations, $status);
    }

    public function index(): JsonResponse
    {
        $data = $this->getAllTaxes(['user']);
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                $dropdownItem = $this->datatablesHelper->buttonDropdownItem('editTax', $item->hash, icon: 'edit', btnText: 'Edit Tax');
                ($item->status == '1')
                    ? $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('setDeactivateTax', $item->hash, 'danger', icon: 'check_circle', btnText: 'Deactivate Tax')
                    : $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('setActivateTax', $item->hash, 'success', icon: 'check_circle', btnText: 'Activate Tax');
                $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('deleteTax', $item->hash, 'danger', icon: 'delete', btnText: 'Delete Tax');
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
            ->rawColumns(['action', 'createdBy', 'status'])
            ->addIndexColumn()
            ->make();
    }

    public function store(array $data, $loggedUser)
    {
        $data['userId'] = $loggedUser->id;
        return $this->taxRepository->create($data);
    }

    public function update(array $data, Tax $tax): bool
    {
        return $tax->update($data);
    }

    public function activate(Tax $tax): bool
    {
        return $tax->update(['status' => '1']);
    }

    public function deactivate(Tax $tax): bool
    {
        return $tax->update(['status' => '0']);
    }

    public function destroy(Tax $tax): ?bool
    {
        return $tax->delete();
    }
}
