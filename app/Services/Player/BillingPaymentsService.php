<?php

namespace App\Services\Player;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\Tax;
use App\Models\User;
use App\Repository\ProductRepository;
use App\Repository\TaxRepository;
use App\Repository\UserRepository;
use App\Services\Service;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Storage;
use Midtrans\Config;
use Midtrans\Snap;
use Yajra\DataTables\Facades\DataTables;

class BillingPaymentsService extends Service
{
    private User $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    public function index()
    {
        $data = $this->user->invoices()->latest();
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                return'<a class="btn btn-sm btn-outline-secondary" href="' . route('billing-and-payments.show', $item->id) . '">
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
                $badge = '';
                if ($item->status == 'Open') {
                    $badge = '<span class="badge badge-pill badge-info">'.$item->status.'</span>';
                } elseif ($item->status == 'Paid') {
                    $badge = '<span class="badge badge-pill badge-success">'.$item->status.'</span>';
                } elseif ($item->status == 'Past Due') {
                    $badge = '<span class="badge badge-pill badge-warning">'.$item->status.'</span>';
                } elseif ($item->status == 'Uncollectible') {
                    $badge = '<span class="badge badge-pill badge-danger">'.$item->status.'</span>';
                }
                return $badge;
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
