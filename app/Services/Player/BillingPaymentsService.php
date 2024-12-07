<?php

namespace App\Services\Player;

use App\Models\Invoice;
use App\Models\User;
use App\Services\DatatablesService;
use App\Services\Service;
use Yajra\DataTables\Facades\DataTables;

class BillingPaymentsService extends Service
{
    private User $user;
    private DatatablesService $datatablesService;
    public function __construct(User $user, DatatablesService $datatablesService)
    {
        $this->user = $user;
        $this->datatablesService = $datatablesService;
    }
    public function index()
    {
        $data = $this->user->invoices()->latest();
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                return'<a class="btn btn-sm btn-outline-secondary" href="' . route('billing-and-payments.show', $item->hash) . '">
                            <span class="material-icons mr-2">
                                visibility
                            </span>
                            view invoice
                        </a>';
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
                return $this->convertToDatetime($item->updatedAt);
            })
            ->editColumn('status', function ($item) {
                return $this->datatablesService->invoiceStatus($item->status);
            })
            ->rawColumns(['action', 'ammount','dueDate', 'status', 'createdAt','updatedAt'])
            ->addIndexColumn()
            ->make();
    }

    public function openInvoices()
    {
        return $this->user->invoices()->where('status', 'Open')->get();
    }

    public function show(Invoice $invoice)
    {
        $createdAt = $this->convertToDatetime($invoice->created_at);
        $dueDate = $this->convertToDatetime($invoice->dueDate);
        $updatedAt = $this->convertToDatetime($invoice->updated_at);
        $createdDate = $this->convertToDate($invoice->created_at);

        return compact('invoice', 'createdAt', 'dueDate', 'updatedAt', 'createdDate');
    }
}
