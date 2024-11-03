<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\Tax;
use App\Repository\ProductRepository;
use App\Repository\TaxRepository;
use App\Repository\UserRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Storage;
use Midtrans\Config;
use Midtrans\Snap;
use Yajra\DataTables\Facades\DataTables;

class InvoiceService extends Service
{
    private ProductRepository $productRepository;
    private TaxRepository $taxRepository;
    private UserRepository $userRepository;
    private Invoice $invoice;
    public function __construct(ProductRepository $productRepository, TaxRepository $taxRepository, UserRepository $userRepository, Invoice $invoice)
    {
        Config::$serverKey    = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized  = config('services.midtrans.isSanitized');
        Config::$is3ds        = config('services.midtrans.is3ds');

        $this->productRepository = $productRepository;
        $this->taxRepository = $taxRepository;
        $this->userRepository = $userRepository;
        $this->invoice = $invoice;
    }
    public function index()
    {
        $data = Invoice::with('receiverUser', 'creatorUser')->latest();
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                $paidButton =
                    '<button type="submit" class="dropdown-item setStatus" id="'.$item->id.'" data-status="paid">
                            <span class="material-icons text-success">check_circle</span>
                            Mark as Paid
                    </button>';
                $uncollectibleButton =
                    '<button type="submit" class="dropdown-item setStatus" id="'.$item->id.'" data-status="uncollectible">
                            <span class="material-icons text-danger">check_circle</span>
                            Mark as Uncollectible
                    </button>';
                $openButton =
                    '<button type="submit" class="dropdown-item setStatus" id="'.$item->id.'" data-status="open">
                            <span class="material-icons text-info">check_circle</span>
                            Mark as Open
                    </button>';

                $statusButton = '';
                if ($item->status == 'Open') {
                    $statusButton = $paidButton. ''. $uncollectibleButton;
                } elseif ($item->status == 'Paid') {
                    $statusButton = $uncollectibleButton;
                } elseif ($item->status == 'Uncollectible') {
                    $statusButton = $paidButton.''.$openButton;
                } elseif ($item->status == 'Past Due') {
                    $statusButton = $openButton.''.$uncollectibleButton;
                }
                return '<div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item edit" href="' . route('invoices.edit',$item->id) . '" type="button">
                                    <span class="material-icons">edit</span>
                                    Edit Invoice
                                </a>
                                <a class="dropdown-item edit" href="' . route('invoices.show', $item->id) . '" type="button">
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
                return $this->convertToDatetime($item->updated_at);
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

    public function invoiceForms()
    {
        $players = $this->userRepository->getAllPlayers();
        $taxes = $this->taxRepository->getAll();
        $products = $this->productRepository->getAll();
        return compact('players', 'taxes', 'products');
    }

    public function store(array $data, $creatorUserIdd, $academyId)
    {
        $data['creatorUserId'] = $creatorUserIdd;
        $data['academyId'] = $academyId;
        $data['invoiceNumber'] = $this->generateInvoiceNumber();
        $data['dueDate'] = $this->getNextDayTimestamp();
        $data['subtotal'] = 0;

        foreach ($data['products'] as $product) {
            $data['subtotal'] = $data['subtotal'] + $product['ammount'];
        }
        $data['ammountDue'] = $data['subtotal'];

        if ($data['taxId']){
            $tax = $this->taxRepository->find($data['taxId']);
            $data['totalTax'] = $data['subtotal'] * $tax->percentage/100;
            $data['ammountDue'] = $data['ammountDue'] + $data['totalTax'];
        }else{
            $data['taxId'] = null;
            $data['totalTax'] = 0;
        }

        $invoice = Invoice::create($data);

        foreach ($data['products'] as $product){
            $invoice->products()->attach($product['productId'], [
                'qty' => $product['qty'],
                'ammount' => $product['ammount']
            ]);

            $productDetail = $this->productRepository->find($product['productId']);
            if ($productDetail->priceOption == 'subscription'){
                $subscription = $this->storeSubscription($data['receiverUserId'], $product['ammount'], $product['productId'], $data['taxId']);
                $invoice->subscriptions()->attach($subscription->id);
            }
        }
        $this->midtransPayment($data, $invoice);

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

    public function storeSubscription($userId, $ammount, $productId, $taxId){
        $data = [];
        $data['startDate'] = $this->getNowDate();
        $data['ammountDue'] = $ammount;
        $data['status'] = 'scheduled';
        $data['userId'] = $userId;
        $data['productId'] = $productId;
        $data['taxId'] = $taxId;

        $product = $this->productRepository->find($productId);

        if ($product->subscriptionCycle == 'monthly'){
            $data['cycle'] =  'monthly';
            $data['nextDueDate'] = $this->getNowDate()->addMonthsNoOverflow(1);
        } elseif ($product->subscriptionCycle == 'quarterly'){
            $data['cycle'] =  'quarterly';
            $data['nextDueDate'] = $this->getNowDate()->addMonthsNoOverflow(3);
        } elseif ($product->subscriptionCycle == 'semianually'){
            $data['cycle'] =  'semianually';
            $data['nextDueDate'] = $this->getNowDate()->addMonthsNoOverflow(6);
        } elseif ($product->subscriptionCycle == 'anually'){
            $data['cycle'] =  'anually';
            $data['nextDueDate'] = $this->getNowDate()->addMonthsNoOverflow(12);
        }

        return Subscription::create($data);
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
        $midtrans = [
            'transaction_details' => [
                'order_id' => $data['invoiceNumber'],
                'gross_amount' => (int) $data['ammountDue'],
            ],
            'customer_details' => [
                'first_name' => $invoice->receiverUser->firstName,
                'email' => $invoice->receiverUser->email
            ],
            'enabled_payments' => [
                'gopay', 'bank_transfer', "indomaret", "danamon_online", "akulaku", "shopeepay", "kredivo", "uob_ezpay","other_qris"
            ],
            'vtweb' => []
        ];

        try {
            $snaptoken = Snap::getSnapToken($midtrans);
            $invoice['snapToken'] = $snaptoken;
            return $invoice->save();

//            Mail::to($data['email'])->send(new PayBookingTrfMail($booking));

//            return redirect()->route('pay-booking', $booking->transaction_code);
        }
        catch (Exception $e){
            return $e->getMessage();
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

        $invoiceSubscriptions = $invoice->subscriptions()->get();

        foreach ($invoiceSubscriptions as $subscription){
            if (!in_array($subscription->id, $data['products'])){
                $invoice->subscriptions()->detach($subscription->id);
                Subscription::destroy($subscription->id);
            }
        }

        foreach ($data['products'] as $product){
            $productDetail = $this->productRepository->find($product['productId']);

            if ($productDetail->priceOption == 'subscription'){
                if ($this->checkSubscriptionIsExist($product['productId'], $data['receiverUserId']) == null){
                    $subscription = $this->storeSubscription($data['receiverUserId'], $product['ammount'], $product['productId']);
                    $invoice->subscriptions()->attach($subscription->id);
                }
            }
        }
        $invoice->update($data);
        $this->midtransPayment($data, $invoice);
        return $invoice;
    }

    public function checkSubscriptionIsExist($productId, $userId){
        return Subscription::where('productId', $productId)
            ->where('userId', $userId)
            ->exists();
    }

    public function paid(Invoice $invoice)
    {
        return $invoice->update(['status' => 'Paid']);
    }

    public function uncollectible(Invoice $invoice)
    {
        return $invoice->update(['status' => 'Uncollectible']);
    }

    public function open(Invoice $invoice)
    {
        $data['invoiceNumber'] = $invoice->invoiceNumber;
        $data['ammountDue'] = $invoice->ammountDue;
        $dueDate = $this->getNextDayTimestamp();

        // refresh midtrans payment token
        $this->midtransPayment($data, $invoice);

        return $invoice->update([
            'status' => 'Open',
            'dueDate' => $dueDate
        ]);
    }
    public function pastDue(Invoice $invoice)
    {
        return $invoice->update(['status' => 'Past Due']);
    }
    public function destroy(Invoice $invoice)
    {
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
                         <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item edit" href="' . route('invoices.show-archived', $item->id) . '" type="button">
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
                        </div>';
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
