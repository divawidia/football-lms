<?php

namespace App\Services\Player;

use App\Helpers\DatatablesHelper;
use App\Models\User;
use App\Services\Service;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class BillingPaymentsService extends Service
{
    private User $user;
    private DatatablesHelper $datatablesHelper;
    public function __construct(User $user, DatatablesHelper $datatablesHelper)
    {
        $this->user = $user;
        $this->datatablesHelper = $datatablesHelper;
    }
    public function index(): JsonResponse
    {
        return Datatables::of($this->user->invoices()->latest())
            ->addColumn('action', function ($item) {
                return $this->datatablesHelper->buttonTooltips(route('billing-and-payments.show', $item->hash), "View Invoice", "visibility");
            })
            ->editColumn('ammount', function ($item) {
                return $this->priceFormat($item->ammountDue);
            })
            ->editColumn('dueDate', function ($item) {
                return $this->convertToDatetime($item->dueDate);
            })
            ->editColumn('createdAt', function ($item) {
                return $this->convertToDatetime($item->created_at);
            })
            ->editColumn('updatedAt', function ($item) {
                return $this->convertToDatetime($item->updated_at);
            })
            ->editColumn('status', function ($item) {
                return $this->datatablesHelper->invoiceStatus($item->status);
            })
            ->rawColumns(['action', 'status'])
            ->addIndexColumn()
            ->make();
    }

    public function openInvoices(): Collection
    {
        return $this->user->invoices()->where('status', 'Open')->get();
    }
}
