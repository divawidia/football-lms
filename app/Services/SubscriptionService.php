<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\Tax;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Storage;
use Midtrans\Config;
use Midtrans\Snap;
use Yajra\DataTables\Facades\DataTables;

class SubscriptionService extends Service
{
    private Product $product;
    private Subscription $subscription;
    private Invoice $invoice;
    private Tax $tax;
    private InvoiceService $invoiceService;
    public function __construct(Product $product, Subscription $subscription, Invoice $invoice, Tax $tax, InvoiceService $invoiceService)
    {
        $this->product = $product;
        $this->invoice = $invoice;
        $this->tax = $tax;
        $this->invoiceService = $invoiceService;
    }
    public function index()
    {
        $data = Subscription::with('user')->latest();
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                $cancelButton ='
                        <button type="button" class="dropdown-item cancelSubscription" id="'.$item->id.'">
                            <span class="material-icons text-danger">check_circle</span>
                            Cancel Subscription
                        </button>';
                $continueButton =
                        '<button type="button" class="dropdown-item continueSubscription" id="'.$item->id.'">
                            <span class="material-icons text-success">check_circle</span>
                            Continue Subscription
                        </button>';

                $statusButton = '';
                if ($item->status == 'scheduled') {
                    $statusButton = $cancelButton;
                } elseif ($item->status == 'unsubscribed') {
                    $statusButton = $continueButton;
                }
                return
                    '<div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item edit" href="' . route('subscriptions.show', $item->id) . '" type="button">
                                    <span class="material-icons">visibility</span>
                                    Show Subscription
                                </a>
                                ' . $statusButton . '
                                <button type="button" class="dropdown-item deleteSubscription" id="' . $item->id . '">
                                    <span class="material-icons text-danger">delete</span>
                                    Delete subscription
                                </button>
                        </div>';
            })
            ->editColumn('name', function ($item) {
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
                                            <small class="js-lists-values-email text-50">' . $item->user->roles[0]['name'] . '</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            })
            ->editColumn('email', function ($item) {
                return $item->user->email;
            })
            ->editColumn('product', function ($item) {
                return $item->product->productName;
            })
            ->editColumn('amountDue', function ($item) {
                return $this->priceFormat($item->ammountDue);
            })
            ->editColumn('startDate', function ($item) {
                return $this->convertToDatetime($item->startDate);
            })
            ->editColumn('nextDueDate', function ($item) {
                return $this->convertToDatetime($item->nextDueDate);
            })
            ->editColumn('createdAt', function ($item) {
                return $this->convertToDatetime($item->created_at);
            })
            ->editColumn('updatedAt', function ($item) {
                return $this->convertToDatetime($item->updatedAt);
            })
            ->editColumn('status', function ($item) {
                $badge = '';
                if ($item->status == 'scheduled') {
                    $badge = '<span class="badge badge-pill badge-success">Scheduled</span>';
                } elseif ($item->status == 'unsubscribed') {
                    $badge = '<span class="badge badge-pill badge-danger">Unsubscribed</span>';
                }
                return $badge;
            })
            ->rawColumns(['action', 'email', 'name', 'product', 'amountDue', 'startDate', 'nextDueDate','status', 'createdAt','updatedAt'])
            ->addIndexColumn()
            ->make();
    }

    public function invoices(Subscription $subscription){
        return Datatables::of($subscription->invoices)
            ->addColumn('action', function ($item) {
                return
                    '<a class="btn btn-sm btn-outline-secondary" href="' . route('invoices.show', $item->id) . '" data-toggle="tooltip" data-placement="bottom" title="Show subscription detail">
                        <span class="material-icons">
                            visibility
                        </span>
                    </a>';
            })
            ->editColumn('name', function ($item) {
                return '
                            <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->receiverUser->foto) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->receiverUser->firstName . ' ' . $item->receiverUser->lastName . '</strong></p>
                                            <small class="js-lists-values-email text-50">' . $item->receiverUser->roles[0]['name'] . '</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            })
            ->editColumn('email', function ($item) {
                return $item->receiverUser->email;
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
            ->rawColumns(['action', 'email', 'ammount','dueDate', 'name', 'status', 'createdAt','updatedAt'])
            ->addIndexColumn()
            ->make();
    }

    public function show(Subscription $subscription)
    {
        $createdAt = $this->convertToDatetime($subscription->created_at);
        $nextDueDate = $this->convertToDatetime($subscription->nextDueDate);
        $startDate = $this->convertToDatetime($subscription->startDate);
        $updatedAt = $this->convertToDatetime($subscription->updated_at);

        return compact('subscription', 'createdAt', 'nextDueDate', 'updatedAt', 'startDate');
    }

    public function scheduled(Subscription $subscription)
    {
        return $subscription->update(['status' => 'scheduled']);
    }

    public function unsubscribed(Subscription $subscription)
    {
        return $subscription->update(['status' => 'unsubscribed']);
    }

    public function createNewInvoice(Subscription $subscription, $creatorUserIdd, $academyId)
    {
        if ($subscription->status == 'scheduled'){
            $data['creatorUserId'] = $creatorUserIdd;
            $data['academyId'] = $academyId;
            $data['invoiceNumber'] = $this->generateInvoiceNumber();
            $data['subtotal'] = $subscription->product->price;
            $data['ammountDue'] = $data['subtotal'];

            if ($subscription->taxId != null){
                $tax = $this->tax->getTaxDetail($subscription->taxId);
                $data['totalTax'] = $data['subtotal'] * $tax->percentage/100;
                $data['ammountDue'] = $data['ammountDue'] + $data['totalTax'];
            }else{
                $data['taxId'] = null;
                $data['totalTax'] = 0;
            }

            $invoice = Invoice::create($data);
            $invoice->products()->attach($subscription->productId, [
                'qty' => 1,
                'ammount' => $data['subtotal']
            ]);
            $invoice->subscriptions()->attach($subscription->id);

            $this->invoiceService->midtransPayment($data, $invoice);

            return $invoice;
        }
    }
}
