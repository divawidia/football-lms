<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\Subscription;
use App\Notifications\Invoices\Admin\InvoiceGeneratedForAdmin;
use App\Notifications\Invoices\Player\InvoiceGeneratedForPlayer;
use App\Notifications\Subscriptions\Admin\SubscriptionRenewedForAdmin;
use App\Notifications\Subscriptions\Player\SubscriptionRenewedForPlayer;
use App\Repository\InvoiceRepository;
use App\Repository\ProductRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\TaxRepository;
use App\Repository\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;

class SubscriptionService extends Service
{
    private SubscriptionRepository $subscriptionRepository;
    private InvoiceRepository $invoiceRepository;
    private TaxRepository $taxRepository;
    private InvoiceService $invoiceService;
    private UserRepository $userRepository;
    private ProductRepository $productRepository;
    private DatatablesHelper $datatablesHelper;

    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        InvoiceRepository      $invoiceRepository,
        TaxRepository          $taxRepository,
        InvoiceService         $invoiceService,
        UserRepository         $userRepository,
        ProductRepository      $productRepository,
        DatatablesHelper       $datatablesHelper)
    {
        $this->invoiceRepository = $invoiceRepository;
        $this->taxRepository = $taxRepository;
        $this->invoiceService = $invoiceService;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->userRepository = $userRepository;
        $this->productRepository = $productRepository;
        $this->datatablesHelper = $datatablesHelper;
    }

    public function index(): JsonResponse
    {
        return Datatables::of($this->subscriptionRepository->getAll())
            ->addColumn('action', function ($item) {
                $dropdownItem = $this->datatablesHelper->linkDropdownItem(route: route('subscriptions.show', $item->hash), icon: 'visibility', btnText: 'Show Subscription');
                $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('edit-tax', $item->hash, icon: 'edit', btnText: 'Edit subscriptions tax');
                if ($item->status == 'scheduled') {
                    $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('cancelSubscription', $item->hash, iconColor: 'danger', icon: 'check_circle', btnText: 'Cancel Subscription');
                } elseif ($item->status == 'unsubscribed') {
                    $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('continueSubscription', $item->hash, iconColor: 'success',icon: 'check_circle', btnText: 'Continue Subscription');
                }
                $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('deleteSubscription', $item->hash, 'danger', icon: 'delete', btnText: 'Delete players subscription');
                return $this->datatablesHelper->dropdown(function () use ($dropdownItem) {
                    return $dropdownItem;
                });
            })
            ->editColumn('name', function ($item) {
                return ($item->user) ? $this->datatablesHelper->name($item->user->foto, $this->getUserFullName($item->user), $item->user->roles[0]['name'], route('player-managements.show', $item->user->player->hash)) : 'Deleted Player';
            })
            ->editColumn('email', function ($item) {
                return ($item->user) ? $item->user->email : 'Deleted Player';
            })
            ->editColumn('product', function ($item) {
                return ($item->product) ? $item->product->productName : 'Deleted Product';
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
                return $this->convertToDatetime($item->updated_at);
            })
            ->editColumn('status', function ($item) {
                return $this->subscriptionStatus($item);
            })
            ->rawColumns(['action', 'name', 'status'])
            ->addIndexColumn()
            ->make();
    }

    private function subscriptionStatus(Subscription $subscription): string
    {
        if ($subscription->status == 'Scheduled') {
            $badge = '<span class="badge badge-pill badge-success">'.$subscription->status.'</span>';
        } elseif ($subscription->status == 'Unsubscribed') {
            $badge = '<span class="badge badge-pill badge-danger">'.$subscription->status.'</span>';
        } else {
            $badge = '<span class="badge badge-pill badge-warning">'.$subscription->status.'</span>';
        }
        return $badge;
    }

    public function playerIndex($user): JsonResponse
    {
        return Datatables::of($user->subscriptions)
            ->editColumn('product', function ($item) {
                return ($item->product) ? $item->product->productName : 'Deleted Product';
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
                return $this->convertToDatetime($item->updated_at);
            })
            ->editColumn('status', function ($item) {
                return $this->subscriptionStatus($item);
            })
            ->rawColumns(['product', 'status'])
            ->addIndexColumn()
            ->make();
    }

    public function invoices(Subscription $subscription): JsonResponse
    {
        return Datatables::of($subscription->invoices)
            ->addColumn('action', function ($item) {
                return $this->datatablesHelper->buttonTooltips(route('invoices.show', $item->hash), "Show subscription detail", "visibility");
            })
            ->editColumn('name', function ($item) {
                return ($item->receiverUser) ? $this->datatablesHelper->name($item->receiverUser->foto, $this->getUserFullName($item->receiverUser), $item->receiverUser->roles[0]['name'], route('player-managements.show', $item->receiverUser->player->hash)) : 'Deleted Player';
            })
            ->editColumn('email', function ($item) {
                return ($item->receiverUser) ? $item->receiverUser->email: 'Deleted Player';
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
                return $this->datatablesHelper->invoiceStatus($item->status);
            })
            ->rawColumns(['action', 'name', 'status'])
            ->addIndexColumn()
            ->make();
    }

    public function getAvailablePlayerSubscriptionProduct($userId): Collection|array
    {
        return $this->productRepository->getAvailablePlayerSubscriptionProduct($userId);
    }

    public function scheduled(Subscription $subscription, $creatorUserId = null, $academyId = null): bool
    {
        //create new invoice when where the subscription is set to scheduled is after the next due date
        if ($this->getNowDate() > $subscription->nextDueDate) {
            $this->createNewInvoice($subscription, $creatorUserId, $academyId);
        }
        return $subscription->update(['status' => 'Scheduled', 'isReminderNotified' => '0']);
    }

    public function unsubscribed(Subscription $subscription): bool
    {
        return $subscription->update(['status' => 'Unsubscribed']);
    }

    public function renewSubscription(Subscription $subscription): bool
    {
        $this->createNewInvoice($subscription, null, academyData()->id);
        return $subscription->update(['status' => 'Pending Payment']);
    }

    public function createNewInvoice(Subscription $subscription, $creatorUserId, $academyId)
    {
        $data['creatorUserId'] = $creatorUserId;
        $data['receiverUserId'] = $subscription->userId;
        $data['academyId'] = $academyId;
        $data['invoiceNumber'] = $this->generateInvoiceNumber();
        $data['dueDate'] = $this->getNextDayTimestamp();
        $data['subtotal'] = $subscription->product->price;
        $data['ammountDue'] = $data['subtotal'];

        if ($subscription->taxId != null) {
            $tax = $this->taxRepository->find($subscription->taxId);
            $data['taxId'] = $subscription->taxId;
            $data['totalTax'] = $data['subtotal'] * $tax->percentage / 100;
            $data['ammountDue'] = $data['ammountDue'] + $data['totalTax'];
        } else {
            $data['taxId'] = null;
            $data['totalTax'] = 0;
        }

        $invoice = $this->invoiceRepository->create($data);
        $invoice->products()->attach($subscription->productId, [
            'qty' => 1,
            'ammount' => $data['subtotal']
        ]);
        $invoice->subscriptions()->attach($subscription->id);

        $product = $this->productRepository->find($subscription->productId);

        if ($product->subscriptionCycle == 'monthly') {
            $data['cycle'] = 'monthly';
            $data['nextDueDate'] = $this->getNowDate()->addMonthsNoOverflow(1);
        } elseif ($product->subscriptionCycle == 'quarterly') {
            $data['cycle'] = 'quarterly';
            $data['nextDueDate'] = $this->getNowDate()->addMonthsNoOverflow(3);
        } elseif ($product->subscriptionCycle == 'semianually') {
            $data['cycle'] = 'semianually';
            $data['nextDueDate'] = $this->getNowDate()->addMonthsNoOverflow(6);
        } elseif ($product->subscriptionCycle == 'anually') {
            $data['cycle'] = 'anually';
            $data['nextDueDate'] = $this->getNowDate()->addMonthsNoOverflow(12);
        }

        $subscription['nextDueDate'] = $data['nextDueDate'];
        $subscription->save();

        $this->invoiceService->midtransPayment($data, $invoice);

        $invoice->receiverUser->notify(new InvoiceGeneratedForPlayer($invoice));
        Notification::send($this->userRepository->getAllAdminUsers(), new InvoiceGeneratedForAdmin($invoice));
        $invoice->receiverUser->notify(new SubscriptionRenewedForPlayer($invoice, $subscription));
        Notification::send($this->userRepository->getAllAdminUsers(), new SubscriptionRenewedForAdmin($invoice, $subscription));
        return $invoice;
    }

    public function updateTax(array $data, Subscription $subscription): bool
    {
        return $subscription->update([
            'taxId' => $data['taxId']
        ]);
    }

    public function destroy(Subscription $subscription): ?bool
    {
        return $subscription->delete();
    }
}
