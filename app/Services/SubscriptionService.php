<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use App\Notifications\Invoices\InvoiceGeneratedAdmin;
use App\Notifications\Invoices\InvoiceGeneratedPlayer;
use App\Notifications\Subscriptions\SubscriptionCreatedAdmin;
use App\Notifications\Subscriptions\SubscriptionCreatedPlayer;
use App\Notifications\Subscriptions\SubscriptionRenewedAdmin;
use App\Notifications\Subscriptions\SubscriptionRenewedPlayer;
use App\Repository\InvoiceRepository;
use App\Repository\ProductRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\TaxRepository;
use App\Repository\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SubscriptionService extends Service
{
    private SubscriptionRepository $subscriptionRepository;
    private InvoiceRepository $invoiceRepository;
    private TaxRepository $taxRepository;
    private InvoiceService $invoiceService;
    private UserRepository $userRepository;
    private ProductRepository $productRepository;

    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        InvoiceRepository      $invoiceRepository,
        TaxRepository          $taxRepository,
        InvoiceService         $invoiceService,
        UserRepository         $userRepository,
        ProductRepository      $productRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
        $this->taxRepository = $taxRepository;
        $this->invoiceService = $invoiceService;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->userRepository = $userRepository;
        $this->productRepository = $productRepository;
    }

    public function index()
    {
        $data = $this->subscriptionRepository->getAll();
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                $cancelButton = '
                        <button type="button" class="dropdown-item cancelSubscription" id="' . $item->id . '">
                            <span class="material-icons text-danger">check_circle</span>
                            Cancel Subscription
                        </button>';
                $continueButton =
                    '<button type="button" class="dropdown-item continueSubscription" id="' . $item->id . '">
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
                          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                <button type="button" class="dropdown-item edit-tax" id="' . $item->id . '">
                                    <span class="material-icons">edit</span>
                                    Edit subscriptions tax
                                </button>
                                <a class="dropdown-item edit" href="' . route('subscriptions.show', $item->hash) . '" type="button">
                                    <span class="material-icons">visibility</span>
                                    Show Subscription
                                </a>
                                ' . $statusButton . '
                                <button type="button" class="dropdown-item deleteSubscription" id="' . $item->id . '">
                                    <span class="material-icons text-danger">delete</span>
                                    Delete players subscription
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
                return $this->convertToDatetime($item->updated_at);
            })
            ->editColumn('status', function ($item) {
                if ($item->status == 'Scheduled') {
                    $badge = '<span class="badge badge-pill badge-success">'.$item->status.'</span>';
                } elseif ($item->status == 'Unsubscribed') {
                    $badge = '<span class="badge badge-pill badge-danger">'.$item->status.'</span>';
                } else {
                    $badge = '<span class="badge badge-pill badge-warning">'.$item->status.'</span>';
                }
                return $badge;
            })
            ->rawColumns(['action', 'email', 'name', 'product', 'amountDue', 'startDate', 'nextDueDate', 'status', 'createdAt', 'updatedAt'])
            ->addIndexColumn()
            ->make();
    }

    public function playerIndex(User $user)
    {
        $data = $user->subscriptions;
        return Datatables::of($data)
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
                return $this->convertToDatetime($item->updated_at);
            })
            ->editColumn('status', function ($item) {
                if ($item->status == 'Scheduled') {
                    $badge = '<span class="badge badge-pill badge-success">'.$item->status.'</span>';
                } elseif ($item->status == 'Unsubscribed') {
                    $badge = '<span class="badge badge-pill badge-danger">'.$item->status.'</span>';
                } else {
                    $badge = '<span class="badge badge-pill badge-warning">'.$item->status.'</span>';
                }
                return $badge;
            })
            ->rawColumns(['product', 'amountDue', 'startDate', 'nextDueDate', 'status', 'createdAt', 'updatedAt'])
            ->addIndexColumn()
            ->make();
    }

    public function invoices(Subscription $subscription)
    {
        return Datatables::of($subscription->invoices)
            ->addColumn('action', function ($item) {
                return
                    '<a class="btn btn-sm btn-outline-secondary" href="' . route('invoices.show', $item->hash) . '" data-toggle="tooltip" data-placement="bottom" title="Show subscription detail">
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
                    $badge = '<span class="badge badge-pill badge-info">' . $item->status . '</span>';
                } elseif ($item->status == 'Paid') {
                    $badge = '<span class="badge badge-pill badge-success">' . $item->status . '</span>';
                } elseif ($item->status == 'Past Due') {
                    $badge = '<span class="badge badge-pill badge-warning">' . $item->status . '</span>';
                } elseif ($item->status == 'Uncollectible') {
                    $badge = '<span class="badge badge-pill badge-danger">' . $item->status . '</span>';
                }
                return $badge;
            })
            ->rawColumns(['action', 'email', 'ammount', 'dueDate', 'name', 'status', 'createdAt', 'updatedAt'])
            ->addIndexColumn()
            ->make();
    }

    public function show(Subscription $subscription)
    {
        $createdAt = $this->convertToDatetime($subscription->created_at);
        $nextDueDate = $this->convertToDatetime($subscription->nextDueDate);
        $startDate = $this->convertToDatetime($subscription->startDate);
        $updatedAt = $this->convertToDatetime($subscription->updated_at);
        $taxes = $this->taxRepository->getAll();
        $subscription = $subscription->with('user', 'product')->find($subscription->id);

        return compact('subscription', 'createdAt', 'nextDueDate', 'updatedAt', 'startDate', 'taxes');
    }

    public function create()
    {
        $players = $this->userRepository->getAllByRole('player');
        $taxes = $this->taxRepository->getAll();
//        $products = $this->productRepository->getByPriceOption('subscription');
        return compact('players', 'taxes');
    }

    public function getAvailablePlayerSubscriptionProduct($userId)
    {
        return $this->productRepository->getAvailablePlayerSubscriptionProduct($userId);
    }

    public function store(array $data, $creatorUserIdd, $academyId)
    {
        $data['creatorUserId'] = $creatorUserIdd;
        $data['academyId'] = $academyId;
        $data['invoiceNumber'] = $this->generateInvoiceNumber();
        $data['dueDate'] = $this->getNextDayTimestamp();
        $data['subtotal'] = $data['productPrice'];
        $data['ammountDue'] = $data['subtotal'];

        if (array_key_exists('taxId', $data)) {
            $tax = $this->taxRepository->find($data['taxId']);
            $data['totalTax'] = $data['subtotal'] * $tax->percentage / 100;
            $data['ammountDue'] = $data['ammountDue'] + $data['totalTax'];
        } else {
            $data['taxId'] = null;
            $data['totalTax'] = 0;
        }

        $invoice = $this->invoiceRepository->create($data);
        $invoice->products()->attach($data['productId'], [
            'qty' => 1,
            'ammount' => $data['subtotal']
        ]);
        $subscription = $this->storeSubscription($data['receiverUserId'], $data['ammountDue'], $data['productId'], $data['taxId']);
        $invoice->subscriptions()->attach($subscription->id);

        $this->invoiceService->midtransPayment($data, $invoice);

        $playerName = $this->getUserFullName($invoice->receiverUser);
        $allAdmins = $this->userRepository->getAllAdminUsers();

        $this->userRepository->find($data['receiverUserId'])->notify(new InvoiceGeneratedPlayer($invoice, $playerName));
        $this->userRepository->find($data['receiverUserId'])->notify(new SubscriptionCreatedPlayer($invoice, $subscription, $playerName));
        Notification::send($allAdmins, new InvoiceGeneratedAdmin($invoice, $playerName));
        Notification::send($allAdmins, new SubscriptionCreatedAdmin($invoice, $subscription, $playerName));

        return $invoice;
    }

    public function storeSubscription($userId, $ammount, $productId, $taxId)
    {
        $data = [];
        $data['startDate'] = $this->getNowDate();
        $data['ammountDue'] = $ammount;
        $data['userId'] = $userId;
        $data['productId'] = $productId;
        $data['taxId'] = $taxId;

        $product = $this->productRepository->find($productId);

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

        return $this->subscriptionRepository->create($data);
    }

    public function scheduled(Subscription $subscription, $creatorUserId = null, $academyId = null)
    {
        $subscription->update(['status' => 'Scheduled']);
        //create new invoice when where the subscription is set to scheduled is after the next due date
        if ($this->getNowDate() > $subscription->nextDueDate) {
            $this->createNewInvoice($subscription, $creatorUserId, $academyId);
        }
    }

    public function unsubscribed(Subscription $subscription)
    {
        return $subscription->update(['status' => 'Unsubscribed']);
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
        $userDetail = $this->userRepository->find($data['receiverUserId']);

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

        $playerName = $this->getUserFullName($invoice->receiverUser);
        $allAdmins = $this->userRepository->getAllAdminUsers();
        $this->userRepository->find($data['receiverUserId'])->notify(new InvoiceGeneratedPlayer($invoice, $playerName));
        $this->userRepository->find($data['receiverUserId'])->notify(new SubscriptionRenewedPlayer($invoice, $subscription, $playerName));
        Notification::send($allAdmins, new InvoiceGeneratedAdmin($invoice, $playerName));
        Notification::send($allAdmins, new SubscriptionRenewedAdmin($subscription->product->productName, $playerName, $invoice->invoiceNumber, $subscription->id));
        return $invoice;
    }

    public function updateTax(array $data, Subscription $subscription)
    {
        return $subscription->update([
            'taxId' => $data['taxId']
        ]);
    }

    public function destroy(Subscription $subscription)
    {
        return $subscription->delete();
    }
}
