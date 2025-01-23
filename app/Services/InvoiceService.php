<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\Invoice;
use App\Models\User;
use App\Notifications\Invoices\InvoiceArchivedAdmin;
use App\Notifications\Invoices\InvoiceArchivedPlayer;
use App\Notifications\Invoices\InvoiceGeneratedAdmin;
use App\Notifications\Invoices\InvoiceGeneratedPlayer;
use App\Notifications\Invoices\InvoicePaidAdmin;
use App\Notifications\Invoices\InvoicePaidPlayer;
use App\Notifications\Invoices\InvoicePastDueAdmin;
use App\Notifications\Invoices\InvoicePastDuePlayer;
use App\Notifications\Invoices\InvoiceUncollectibleAdmin;
use App\Notifications\Invoices\InvoiceUncollectiblePlayer;
use App\Notifications\Subscriptions\SubscriptionCreatedAdmin;
use App\Notifications\Subscriptions\SubscriptionCreatedPlayer;
use App\Notifications\Subscriptions\SubscriptionPastDueAdmin;
use App\Notifications\Subscriptions\SubscriptionPastDuePlayer;
use App\Notifications\Subscriptions\SubscriptionSchedulledAdmin;
use App\Notifications\Subscriptions\SubscriptionSchedulledPlayer;
use App\Repository\InvoiceRepository;
use App\Repository\ProductRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\TaxRepository;
use App\Repository\UserRepository;
use Exception;
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
    private DatatablesHelper $datatablesService;
    public function __construct(
        ProductRepository      $productRepository,
        TaxRepository          $taxRepository,
        UserRepository         $userRepository,
        Invoice                $invoice,
        InvoiceRepository      $invoiceRepository,
        SubscriptionRepository $subscriptionRepository,
        DatatablesHelper       $datatablesService)
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
        $this->datatablesService = $datatablesService;
    }
    public function index()
    {
        $data = $this->invoiceRepository->getAll();
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                $paidButton =
                    '<button type="submit" class="dropdown-item setStatus" id="'.$item->id.'" data-status="paid">
                            <span class="material-icons text-success">check_circle</span>
                            Mark as Paid
                    </button>';
                $uncollectibleButton =
                    '<button type="submit" class="dropdown-item setUcollectibleStatus" id="'.$item->id.'" data-status="uncollectible">
                            <span class="material-icons text-danger">check_circle</span>
                            Mark as Uncollectible
                    </button>';
                $openButton =
                    '<button type="submit" class="dropdown-item setStatus" id="'.$item->id.'" data-status="open">
                            <span class="material-icons text-info">check_circle</span>
                            Mark as Open
                    </button>';
                $pastDueButton =
                    '<button type="submit" class="dropdown-item setStatus" id="'.$item->id.'" data-status="past-due">
                            <span class="material-icons text-warning">check_circle</span>
                            Mark as Past Due
                    </button>';

                $statusButton = '';
                $editButton = '';

                if ($item->status == 'Open') {
                    $statusButton = $uncollectibleButton;
//                    $editButton = '<a class="dropdown-item edit" href="' . route('invoices.edit',$item->id) . '" type="button">
//                                        <span class="material-icons">edit</span>
//                                        Edit Invoice
//                                    </a>';
                }

                return '<div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                ' . $editButton . '
                                <a class="dropdown-item edit" href="' . route('invoices.show', $item->hash) . '" type="button">
                                    <span class="material-icons">visibility</span>
                                    Show Invoice
                                </a>
                                ' . $statusButton . '
                                <button type="button" class="dropdown-item deleteInvoice" id="' . $item->id . '">
                                    <span class="material-icons text-danger">delete</span>
                                    Archive Invoice
                                </button>
                        </div>';
            })
            ->editColumn('name', function ($item) {
                if ($item->receiverUser) {
                    return $this->datatablesService->name($item->receiverUser->foto, $this->getUserFullName($item->receiverUser), $item->receiverUser->roles[0]['name'], route('player-managements.show', $item->receiverUser->player->hash));
                } else {
                    return 'Deleted Player';
                }

            })
            ->editColumn('email', function ($item) {
                if ($item->receiverUser) {
                    return $item->receiverUser->email;
                } else {
                    return 'Deleted Player';
                }
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
                return $this->datatablesService->invoiceStatus($item->status);
            })
            ->rawColumns(['action', 'email', 'ammount', 'totalTax', 'subtotal','dueDate', 'name', 'status', 'createdAt','updatedAt'])
            ->addIndexColumn()
            ->make();
    }

    public function invoiceForms()
    {
        $players = $this->userRepository->getAll(role: 'player');
        $taxes = $this->taxRepository->getAll();
        $products = $this->productRepository->getByPriceOption('one time payment');
        return compact('players', 'taxes', 'products');
    }

    public function checkPlayerAlreadySubscribed(array $data)
    {
        $player = $this->userRepository->find($data['receiverUserId']);
        $productId = intval(collect($data['products'])->pluck('productId')->all());
        $playerSubscription = collect($player->subscriptions)->pluck('productId')->all();

        return in_array($productId, $playerSubscription);
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
        $playerName = $this->getUserFullName($invoice->receiverUser);

        foreach ($data['products'] as $product){
            $invoice->products()->attach($product['productId'], [
                'qty' => $product['qty'],
                'ammount' => $product['ammount']
            ]);
//
            $productDetail = $this->productRepository->find($product['productId']);
            if ($productDetail->priceOption == 'subscription'){
                $subscription = $this->storeSubscription($data['receiverUserId'], $product['ammount'], $product['productId'], $data['taxId'], $product['qty']);
                $invoice->subscriptions()->attach($subscription->id);

                try {
                    $this->userRepository->find($data['receiverUserId'])->notify(new SubscriptionCreatedPlayer($invoice, $subscription, $playerName));
                    Notification::send($allAdmins, new SubscriptionCreatedAdmin($invoice, $subscription, $playerName));
                } catch (Exception $exception) {
                    Log::error('Something when wrong when sending create invoice notification : '. $exception->getMessage());
                }
            }
        }
        $this->midtransPayment($data, $invoice);

        try {
            $this->userRepository->find($data['receiverUserId'])->notify(new InvoiceGeneratedPlayer($invoice, $playerName));
            Notification::send($allAdmins, new InvoiceGeneratedAdmin($invoice, $playerName));
        } catch (Exception $exception) {
            Log::error('Something when wrong when sending create invoice notification : '. $exception->getMessage());
        }

        return $invoice;
    }

    public function calculateProductAmount(int $qty, $productId){
        $product = $this->productRepository->find($productId);
        $productPrice = $product->price;
        $amount = $qty * $productPrice;
        $cycle = $product->subscriptionCycle;

        if ($cycle == 'monthly'){
            $subscription = '/Month';
        } elseif ($cycle == 'quarterly'){
            $subscription = '/3 Month';
        } elseif ($cycle == 'semianually'){
            $subscription = '/6 Month';
        } elseif ($cycle == 'anually'){
            $subscription = '/Year';
        } else {
            $subscription = '';
        }

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

    public function storeSubscription($userId, $ammount, $productId, $taxId, $quantity)
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
            $data['nextDueDate'] = $this->getNowDate()->addMonthsNoOverflow(1*$quantity);
        } elseif ($product->subscriptionCycle == 'quarterly') {
            $data['cycle'] = 'quarterly';
            $data['nextDueDate'] = $this->getNowDate()->addMonthsNoOverflow(3*$quantity);
        } elseif ($product->subscriptionCycle == 'semianually') {
            $data['cycle'] = 'semianually';
            $data['nextDueDate'] = $this->getNowDate()->addMonthsNoOverflow(6*$quantity);
        } elseif ($product->subscriptionCycle == 'anually') {
            $data['cycle'] = 'anually';
            $data['nextDueDate'] = $this->getNowDate()->addMonthsNoOverflow(12*$quantity);
        }

        return $this->subscriptionRepository->create($data);
    }

    public function show(Invoice $invoice)
    {
        $createdAt = $this->convertToDatetime($invoice->created_at);
        $dueDate = $this->convertToDatetime($invoice->dueDate);
        $updatedAt = $this->convertToDatetime($invoice->updated_at);
        $createdDate = $this->convertToDate($invoice->created_at);

        return compact('invoice', 'createdAt', 'dueDate', 'updatedAt', 'createdDate');
    }

    public function showArchived(string $invoiceId)
    {
        $invoice = $this->invoice->findDeletedData($invoiceId);
        $createdAt = $this->convertToDatetime($invoice->created_at);
        $dueDate = $this->convertToDatetime($invoice->dueDate);
        $updatedAt = $this->convertToDatetime($invoice->updated_at);
        $createdDate = $this->convertToDate($invoice->created_at);

        return compact('invoice', 'createdAt', 'dueDate', 'updatedAt', 'createdDate');
    }

    public function midtransPayment(array $data, Invoice $invoice){
//        dd($data);
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

    public function update(array $data, Invoice $invoice)
    {
        $data['subtotal'] = 0;
        $data['invoiceNumber'] = $invoice->invoiceNumber;
        $data['dueDate'] = $this->getNextDayTimestamp();

        foreach ($data['products'] as $product) {
            $data['subtotal'] = $data['subtotal'] + $product['ammount'];
        }
        $data['ammountDue'] = $data['subtotal'];

        if ($data['taxId'] != null){
            $tax = $this->taxRepository->find($data['taxId']);
            $data['totalTax'] = $data['subtotal'] * $tax->percentage/100;
            $data['ammountDue'] = $data['ammountDue'] + $data['totalTax'];
        }else{
            $data['totalTax'] = 0;
        }

        $invoice->products()->sync($data['products']);

//        $invoiceSubscriptions = $invoice->subscriptions()->get();
//
//        foreach ($invoiceSubscriptions as $subscription){
//            if (!in_array($subscription->id, $data['products'])){
//                $invoice->subscriptions()->detach($subscription->id);
//                Subscription::destroy($subscription->id);
//            }
//        }
//
//        foreach ($data['products'] as $product){
//            $productDetail = $this->productRepository->find($product['productId']);
//
//            if ($productDetail->priceOption == 'subscription'){
//                if ($this->checkSubscriptionIsExist($product['productId'], $data['receiverUserId']) == null){
//                    $subscription = $this->storeSubscription($data['receiverUserId'], $product['ammount'], $product['productId']);
//                    $invoice->subscriptions()->attach($subscription->id);
//                }
//            }
//        }
        $invoice->update($data);
        $this->midtransPayment($data, $invoice);
        return $invoice;
    }

//    public function checkSubscriptionIsExist($productId, $userId){
//        return Subscription::where('productId', $productId)
//            ->where('userId', $userId)
//            ->exists();
//    }

    public function paid(Invoice $invoice)
    {
        $paymentDetails = $this->getPaymentDetail($invoice->invoiceNumber);
        $playerName = $this->getUserFullName($invoice->receiverUser);
        $subscription = $invoice->subscriptions()->first();
        $user = $this->userRepository->find($invoice->receiverUserId);

        $invoice->update([
            'status' => 'Paid',
            'paymentMethod' => $paymentDetails->payment_type,
        ]);

        // check if the invoice are subscription invoice
        if ($subscription != null){
            $subscription->update(['status' => 'Scheduled', 'isReminderNotified' => '0']);
            $user->notify(new SubscriptionSchedulledPlayer($subscription, $playerName));
            Notification::send($this->userRepository->getAllAdminUsers(), new SubscriptionSchedulledAdmin($subscription, $playerName));
        }

        //send paid invoice notification
        $user->notify(new InvoicePaidPlayer($invoice, $playerName));
        Notification::send($this->userRepository->getAllAdminUsers(), new InvoicePaidAdmin($invoice, $playerName));
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

        $playerName = $this->getUserFullName($invoice->receiverUser);
        $this->userRepository->find($invoice->receiverUserId)->notify(new InvoiceUncollectiblePlayer(
            $invoice,
            $playerName
        ));
        Notification::send($this->userRepository->getAllAdminUsers(), new InvoiceUncollectibleAdmin(
            $invoice,
            $playerName,
        ));
        return $invoice;
    }

    public function getPaymentDetail($invoiceNumber)
    {
        return Transaction::status($invoiceNumber);
    }

    public function open(Invoice $invoice, User $user)
    {
        $data['invoiceNumber'] = $this->generateInvoiceNumber();
        $data['ammountDue'] = $invoice->ammountDue;
        $dueDate = $this->getNextDayTimestamp();

        // refresh midtrans payment token
        $this->midtransPayment($data, $invoice);
//        $this->userRepository->find($invoice->receiverUserId)->notify(new InvoiceOpenPlayer(
//            $this->convertToDatetime($invoice->dueDate),
//            $invoice->id,
//            $invoice->invoiceNumber,
//        ));

//        $adminUsers = $this->userRepository->getAllByRole('admin');
//        $superAdminUsers = $this->userRepository->getAllByRole('Super-Admin');
//        $adminName = $user->firstName.' '.$user->lastName;

//        Notification::send($adminUsers, new InvoiceOpenAdmin(
//            $adminName,
//            $invoice->id,
//            $invoice->invoiceNumber,
//        ));
//        Notification::send($superAdminUsers, new InvoiceOpenAdmin(
//            $adminName,
//            $invoice->id,
//            $invoice->invoiceNumber,
//        ));

        return $invoice->update([
            'status' => 'Open',
            'dueDate' => $dueDate,
            'invoiceNumber' => $data['invoiceNumber']
        ]);
    }

    public function pastDue(Invoice $invoice)
    {
//        Transaction::cancel($invoice->invoiceNumber);
        $invoice->update(['status' => 'Past Due']);

        $playerName = $this->getUserFullName($invoice->receiverUser);
        $subscription = $invoice->subscriptions()->first();
        $user = $this->userRepository->find($invoice->receiverUserId);

        if ($subscription != null){
            $subscription->update(['status' => 'Past Due Payment']);
            $user->notify(new SubscriptionPastDuePlayer($subscription, $invoice));
            Notification::send($this->userRepository->getAllAdminUsers(), new SubscriptionPastDueAdmin($subscription, $invoice,$playerName));
        }

        $this->userRepository->find($invoice->receiverUserId)->notify(new InvoicePastDuePlayer(
            $invoice,
            $playerName,
        ));
        Notification::send($this->userRepository->getAllAdminUsers(), new InvoicePastDueAdmin(
            $invoice,
            $playerName,
        ));
        return $invoice;
    }

    public function destroy(Invoice $invoice)
    {
        $playerName = $this->getUserFullName($invoice->receiverUser);
        $this->userRepository->find($invoice->receiverUserId)->notify(new InvoiceArchivedPlayer(
            $invoice,
            $playerName,
        ));
        Notification::send($this->userRepository->getAllAdminUsers(), new InvoiceArchivedAdmin(
            $invoice,
            $playerName,
        ));
        return $invoice->delete();
    }

    public function deletedDataIndex(){
        $data = Invoice::onlyTrashed()->with('receiverUser', 'creatorUser')->latest();
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                return
                    '<div class="dropdown">
                         <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                         </button>
                         <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item edit" href="' . route('invoices.show-archived', $item->hash) . '" type="button">
                                <span class="material-icons">visibility</span>
                                Show Invoice
                            </a>
                            <button type="button" class="dropdown-item restoreInvoice" id="'.$item->id.'">
                                <span class="material-icons text-success">restore</span>
                                Restore Invoice
                            </button>
                            <button type="button" class="dropdown-item forceDeleteInvoice" id="'.$item->id.'">
                                <span class="material-icons text-danger">delete</span>
                                Permanently Delete Invoice
                            </button>
                        </div>
                    </div>';
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesService->name($item->receiverUser->foto, $this->getUserFullName($item->receiverUser), $item->receiverUser->roles[0]['name'], route('player-managements.show', $item->receiverUser->player->hash));
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
            ->rawColumns(['action', 'email', 'ammount','dueDate', 'name', 'status', 'deletedAt','updatedAt'])
            ->addIndexColumn()
            ->make();
    }

    public function restoreData(string $invoiceId){
        $invoice = $this->invoice->findDeletedData($invoiceId);
        return $invoice->restore();
    }

    public function permanentDeleteData(string $invoiceId){
        $invoice = $this->invoice->findDeletedData($invoiceId);
        return $invoice->forceDelete();
    }
}
