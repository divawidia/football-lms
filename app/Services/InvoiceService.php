<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\Invoice;
use App\Notifications\Invoices\Admin\InvoiceArchivedForAdmin;
use App\Notifications\Invoices\Admin\InvoiceGeneratedForAdmin;
use App\Notifications\Invoices\Admin\InvoicePaidForAdmin;
use App\Notifications\Invoices\Admin\InvoicePastDueForAdmin;
use App\Notifications\Invoices\Admin\InvoiceUncollectibleForAdmin;
use App\Notifications\Invoices\Player\InvoiceArchivedForPlayer;
use App\Notifications\Invoices\Player\InvoiceGeneratedForPlayer;
use App\Notifications\Invoices\Player\InvoicePaidForPlayer;
use App\Notifications\Invoices\Player\InvoicePastDueForPlayer;
use App\Notifications\Invoices\Player\InvoiceUncollectibleForPlayer;
use App\Notifications\Subscriptions\Admin\SubscriptionCreatedForAdmin;
use App\Notifications\Subscriptions\Admin\SubscriptionPastDueForAdmin;
use App\Notifications\Subscriptions\Admin\SubscriptionScheduledForAdmin;
use App\Notifications\Subscriptions\Player\SubscriptionCreatedForPlayer;
use App\Notifications\Subscriptions\Player\SubscriptionPastDueForPlayer;
use App\Notifications\Subscriptions\Player\SubscriptionScheduledForPlayer;
use App\Repository\InvoiceRepository;
use App\Repository\ProductRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\TaxRepository;
use App\Repository\UserRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Yajra\DataTables\Facades\DataTables;

class InvoiceService extends Service
{
    private ProductRepository $productRepository;
    private TaxRepository $taxRepository;
    private UserRepository $userRepository;
    private InvoiceRepository $invoiceRepository;
    private SubscriptionRepository $subscriptionRepository;
    private Invoice $invoice;
    private DatatablesHelper $datatablesHelper;
    public function __construct(
        ProductRepository      $productRepository,
        TaxRepository          $taxRepository,
        UserRepository         $userRepository,
        Invoice                $invoice,
        InvoiceRepository      $invoiceRepository,
        SubscriptionRepository $subscriptionRepository,
        DatatablesHelper       $datatablesHelper)
    {
        Config::$serverKey    = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized  = config('services.midtrans.isSanitized');
        Config::$is3ds        = config('services.midtrans.is3ds');

        $this->productRepository = $productRepository;
        $this->taxRepository = $taxRepository;
        $this->userRepository = $userRepository;
        $this->invoice = $invoice;
        $this->invoiceRepository = $invoiceRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->datatablesHelper = $datatablesHelper;
    }
    public function index(): JsonResponse
    {
        $data = $this->invoiceRepository->getAll();
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                $dropdownItem = $this->datatablesHelper->linkDropdownItem(route: route('invoices.show', $item->hash), icon: 'visibility', btnText: 'Show Invoice');
                ($item->status == 'Open')
                    ? $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('setUcollectibleStatus', $item->hash, 'danger', icon: 'check_circle', btnText: 'Mark as Uncollectible')
                    : $dropdownItem .= "";
                $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('deleteInvoice', $item->hash, 'danger', icon: 'delete', btnText: 'Archive Invoice');
                return $this->datatablesHelper->dropdown(function () use ($dropdownItem) {
                    return $dropdownItem;
                });
            })
            ->editColumn('name', function ($item) {
                return ($item->receiverUser)
                    ? $this->datatablesHelper->name($item->receiverUser->foto, $this->getUserFullName($item->receiverUser), $item->receiverUser->roles[0]['name'], route('player-managements.show', $item->receiverUser->player->hash))
                    : 'Deleted Player';
            })
            ->editColumn('email', function ($item) {
                return ($item->receiverUser) ? $item->receiverUser->email : 'Deleted Player';
            })
            ->editColumn('ammount', function ($item) {
                return $this->priceFormat($item->ammountDue);
            })
            ->editColumn('totalTax', function ($item) {
                return $this->priceFormat($item->totalTax);
            })
            ->editColumn('subtotal', function ($item) {
                return $this->priceFormat($item->subtotal);
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
            ->rawColumns(['action', 'name', 'status'])
            ->addIndexColumn()
            ->make();
    }

    public function checkPlayerAlreadySubscribed(array $data)
    {
        $player = $this->userRepository->find($data['receiverUserId']);
        $productId = collect($data['products'])->pluck('productId')->all();
        $productId = array_map('intval', $productId);
        $playerSubscription = collect($player->subscriptions)->pluck('productId')->all();

        return array_intersect($playerSubscription, $productId);
    }

    public function store(array $data, $creatorUserIdd, $academyId)
    {
        $allAdmins = $this->userRepository->getAllAdminUsers();
        $data['creatorUserId'] = $creatorUserIdd;
        $data['academyId'] = $academyId;
        $data['invoiceNumber'] = $this->generateInvoiceNumber();
        $data['dueDate'] = $this->getNextDayTimestamp();
        $data['subtotal'] = 0;


        foreach ($data['products'] as $product) {
            $data['subtotal'] = $data['subtotal'] + $product['ammount'];
        }
        $data['ammountDue'] = $data['subtotal'];

        if (array_key_exists('taxId', $data)){
            $tax = $this->taxRepository->find($data['taxId']);
            $data['totalTax'] = $data['subtotal'] * $tax->percentage/100;
            $data['ammountDue'] = $data['ammountDue'] + $data['totalTax'];
        }else{
            $data['taxId'] = null;
            $data['totalTax'] = 0;
        }

        $invoice = $this->invoiceRepository->create($data);

        foreach ($data['products'] as $product){
            $invoice->products()->attach($product['productId'], [
                'qty' => $product['qty'],
                'ammount' => $product['ammount']
            ]);
            $productDetail = $this->productRepository->find($product['productId']);
            if ($productDetail->priceOption == 'subscription'){
                $subscription = $this->storeSubscription($data['receiverUserId'], $product['ammount'], $product['productId'], $data['taxId'], $product['qty']);
                $invoice->subscriptions()->attach($subscription->id);

                $invoice->receiverUser->notify(new SubscriptionCreatedForPlayer($invoice, $subscription));
                Notification::send($allAdmins, new SubscriptionCreatedForAdmin($invoice, $subscription));
            }
        }
        $this->midtransPayment($data, $invoice);

        $invoice->receiverUser->notify(new InvoiceGeneratedForPlayer($invoice));
        Notification::send($allAdmins, new InvoiceGeneratedForAdmin($invoice));
        return $invoice;
    }

    public function calculateProductAmount(int $qty, $productId): array
    {
        $product = $this->productRepository->find($productId);
        $productPrice = $product->price;
        $amount = $qty * $productPrice;
        $subscriptions = [
            'monthly' => '/Month',
            'quarterly' => '/3 Month',
            'semianually' => '/6 Month',
            'anually' => '/Year'
        ];
        $subscription = $subscriptions[$product->subscriptionCycle] ?? '';
        return compact('productPrice', 'amount', 'subscription');
    }

    public function calculateInvoiceTotal(array $data){
        $data['subtotal'] = 0;

        foreach ($data['products'] as $product) {
            $data['subtotal'] = $data['subtotal'] + $product['ammount'];
        }
        $data['ammountDue'] = $data['subtotal'];

        if ($data['taxId']){
            $tax = $this->taxRepository->find($data['taxId']);
            $data['totalTax'] = $data['subtotal'] * $tax->percentage;
            $data['ammountDue'] = $data['ammountDue'] + $data['totalTax'];
        }

        return $data;
    }

    public function storeSubscription($userId, $amount, $productId, $taxId, $quantity)
    {
        $product = $this->productRepository->find($productId);

        if (!$product) {
            throw new Exception("Product not found.");
        }

        $cycleMapping = [
            'monthly' => 1,
            'quarterly' => 3,
            'semianually' => 6,
            'anually' => 12,
        ];

        $cycle = $product->subscriptionCycle;

        if (!isset($cycleMapping[$cycle])) {
            throw new Exception("Invalid subscription cycle.");
        }

        $data = [
            'startDate' => $this->getNowDate(),
            'ammountDue' => $amount,
            'userId' => $userId,
            'productId' => $productId,
            'taxId' => $taxId,
            'cycle' => $cycle,
            'nextDueDate' => $this->getNowDate()->addMonthsNoOverflow($cycleMapping[$cycle] * $quantity),
        ];

        return $this->subscriptionRepository->create($data);
    }

    public function showArchived(string $id)
    {
        return $this->invoiceRepository->findDeletedData($id);
    }

    public function midtransPayment(array $data, Invoice $invoice)
    {
        $items = [];
        foreach ($invoice->products as $product){
            $items[] = [
                'id' => $product->id,
                'name' => $product->productName,
                'price' => $product->price,
                'quantity' => $product->pivot->qty,
                'subtotal' => $product->pivot->ammount,
            ];
        }

        if ($data['taxId'] != null){
            $tax = $this->taxRepository->find($data['taxId']);
            $items[] = [
                'id' => $tax->id,
                'name' => $tax->taxName.' ~ '.$tax->percentage.'%',
                'price' => $data['totalTax'],
                'quantity' => 1,
                'subtotal' => $data['totalTax'],
            ];
        }

        $billing_address = array(
            'first_name'   => $invoice->receiverUser->firstName,
            'last_name'    => $invoice->receiverUser->lastName,
            'address'      => $invoice->receiverUser->address,
            'city'         => $invoice->receiverUser->city->name,
            'postal_code'  => $invoice->receiverUser->zipCode,
            'phone'        => $invoice->receiverUser->phoneNumber,
            'country_code' => $invoice->receiverUser->country->iso3,
        );

        $customer_details = array(
            'first_name'       => $invoice->receiverUser->firstName,
            'last_name'        => $invoice->receiverUser->lastName,
            'email'            => $invoice->receiverUser->email,
            'phone'            => $invoice->receiverUser->phoneNumber,
            'billing_address'  => $billing_address,
        );

        $midtrans = [
            'transaction_details' => [
                'order_id' => $data['invoiceNumber'],
                'gross_amount' => (int) $data['ammountDue'],
            ],
            'item_details'        => $items,
            'customer_details'    => $customer_details,
            'enabled_payments' => [
                'gopay', 'bank_transfer', "indomaret", "danamon_online", "akulaku", "shopeepay", "kredivo", "uob_ezpay","other_qris"
            ],
            'vtweb' => []
        ];

        try {
            $snaptoken = Snap::getSnapToken($midtrans);
            $invoice['snapToken'] = $snaptoken;
            return $invoice->save();
        }
        catch (Exception $e){
            Log::error('Error in someMethod: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred: '. $e->getMessage()], 500);
        }
    }

    public function paid(Invoice $invoice)
    {
        $paymentDetails = $this->getPaymentDetail($invoice->invoiceNumber);
        $subscription = $invoice->subscriptions()->first();
        $invoice->update([
            'status' => 'Paid',
            'paymentMethod' => $paymentDetails->payment_type,
        ]);

        // check if the invoice are subscription invoice
        if ($subscription != null){
            $subscription->update(['status' => 'Scheduled', 'isReminderNotified' => '0']);
            $subscription->user->notify(new SubscriptionScheduledForPlayer($subscription));
            Notification::send($this->userRepository->getAllAdminUsers(), new SubscriptionScheduledForAdmin($subscription));
        }

        //send paid invoice notification
        $invoice->receiverUser->notify(new InvoicePaidForPlayer($invoice));
        Notification::send($this->userRepository->getAllAdminUsers(), new InvoicePaidForAdmin($invoice));
        return $invoice;
    }

    public function uncollectible(Invoice $invoice)
    {
        try {
            $status = $this->getPaymentDetail($invoice->invoiceNumber)->transaction_status;
            if ($status == 'pending' || $status == 'challenge'){
                Transaction::cancel($invoice->invoiceNumber);
            }
        } catch (Exception $e){
            Log::error('Error in someMethod: ' . $e->getMessage());
        }
        $invoice->update(['status' => 'Uncollectible']);
        $invoice->receiverUser->notify(new InvoiceUncollectibleForPlayer($invoice));
        Notification::send($this->userRepository->getAllAdminUsers(), new InvoiceUncollectibleForAdmin($invoice));
        return $invoice;
    }

    public function getPaymentDetail($invoiceNumber)
    {
        return Transaction::status($invoiceNumber);
    }

    public function checkMidtransInvoiceStatus(Invoice $invoice)
    {
        try {
            $status = $this->getPaymentDetail($invoice->invoiceNumber)->transaction_status;

            return match ($status) {
                'settlement' => $this->paid($invoice),
                'deny', 'cancel' => $this->uncollectible($invoice),
                'expire' => $this->pastDue($invoice),
                default => null,
            };
        } catch (Exception $e){
        }
    }

    public function open(Invoice $invoice): bool
    {
        $data['invoiceNumber'] = $this->generateInvoiceNumber();
        $data['ammountDue'] = $invoice->ammountDue;
        $dueDate = $this->getNextDayTimestamp();

        $this->midtransPayment($data, $invoice);

        return $invoice->update([
            'status' => 'Open',
            'dueDate' => $dueDate,
            'invoiceNumber' => $data['invoiceNumber']
        ]);
    }

    public function pastDue(Invoice $invoice): Invoice
    {
        $invoice->update(['status' => 'Past Due']);
        $subscription = $invoice->subscriptions()->first();

        if ($subscription != null){
            $subscription->update(['status' => 'Past Due Payment']);
            $invoice->receiverUser->notify(new SubscriptionPastDueForPlayer($invoice, $subscription));
            Notification::send($this->userRepository->getAllAdminUsers(), new SubscriptionPastDueForAdmin($invoice, $subscription));
        }

        $invoice->receiverUser->notify(new InvoicePastDueForPlayer($invoice));
        Notification::send($this->userRepository->getAllAdminUsers(), new InvoicePastDueForAdmin($invoice));
        return $invoice;
    }

    public function destroy(Invoice $invoice): ?bool
    {
        $invoice->receiverUser->notify(new InvoiceArchivedForPlayer($invoice));
        Notification::send($this->userRepository->getAllAdminUsers(), new InvoiceArchivedForAdmin($invoice, $invoice->receiverUser,));
        return $invoice->delete();
    }

    public function deletedDataIndex(){
        $data = Invoice::onlyTrashed()->with('receiverUser', 'creatorUser')->latest();
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                $dropdownItem = $this->datatablesHelper->linkDropdownItem(route: route('invoices.show-archived', $item->id), icon: 'visibility', btnText: 'Show Invoice');
                $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('restoreInvoice', $item->id, 'success', icon: 'restore', btnText: 'Restore Invoice');
                $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('forceDeleteInvoice', $item->id, 'danger', icon: 'delete', btnText: 'Permanently Delete Invoice');
                return $this->datatablesHelper->dropdown(function () use ($dropdownItem) {
                    return $dropdownItem;
                });
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesHelper->name($item->receiverUser->foto, $this->getUserFullName($item->receiverUser), $item->receiverUser->roles[0]['name'], route('player-managements.show', $item->receiverUser->player->hash));
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
            ->editColumn('deletedAt', function ($item) {
                return $this->convertToDatetime($item->deleted_at);
            })
            ->editColumn('updatedAt', function ($item) {
                return $this->convertToDatetime($item->updated_at);
            })
            ->editColumn('status', function ($item) {
                return $this->datatablesHelper->invoiceStatus($item->status);
            })
            ->rawColumns(['action', 'name', 'status'])
            ->addIndexColumn()
            ->make();
    }

    public function restoreData(string $invoiceId){
        $invoice = $this->invoiceRepository->findDeletedData($invoiceId);
        return $invoice->restore();
    }

    public function permanentDeleteData(string $invoiceId){
        $invoice = $this->invoiceRepository->findDeletedData($invoiceId);
        return $invoice->forceDelete();
    }
}
