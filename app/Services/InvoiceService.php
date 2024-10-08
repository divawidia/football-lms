<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Subscription;
use App\Models\Tax;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Midtrans\Config;
use Midtrans\Snap;
use Yajra\DataTables\Facades\DataTables;

class InvoiceService extends Service
{
    private Product $product;
    private Tax $tax;
    public function __construct(Product $product, Tax $tax)
    {
        Config::$serverKey    = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized  = config('services.midtrans.isSanitized');
        Config::$is3ds        = config('services.midtrans.is3ds');

        $this->product = $product;
        $this->tax = $tax;
    }
    public function index()
    {
        $data = Invoice::with('receiverUser', 'creatorUser')->latest();
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                $paidButton =
                    '<form action="" method="POST">
                        ' . method_field("PATCH") . '
                        ' . csrf_field() . '
                        <button type="submit" class="dropdown-item">
                            <span class="material-icons text-success">check_circle</span>
                            Mark as Paid
                        </button>
                    </form>';
                $uncollectibleButton =
                    '<form action="" method="POST">
                        ' . method_field("PATCH") . '
                        ' . csrf_field() . '
                        <button type="submit" class="dropdown-item">
                            <span class="material-icons text-danger">check_circle</span>
                            Mark as Uncollectible
                        </button>
                    </form>';

                $statusButton = '';
                if ($item->status == 'Open') {
                    $statusButton = $paidButton. ''. $uncollectibleButton;
                } elseif ($item->status == 'Paid') {
                    $statusButton = $uncollectibleButton;
                } elseif ($item->status == 'Uncollectible') {
                    $statusButton = $paidButton;
                }
                return
                    '<div class="dropdown">
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
                                Delete Invoice
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
                    $badge = '<span class="badge badge-pill badge-warning">'.$item->status.'</span>';
                }
                return $badge;
            })
            ->rawColumns(['action', 'email', 'ammount','dueDate', 'name', 'status', 'updatedAt'])
            ->addIndexColumn()
            ->make();
    }

    public function store(array $data, $creatorUserIdd, $academyId)
    {
        $data['creatorUserId'] = $creatorUserIdd;
        $data['academyId'] = $academyId;
        $data['invoiceNumber'] = $this->generateInvoiceNumber();
        $data['subtotal'] = 0;

        foreach ($data['products'] as $product) {
            $data['subtotal'] = $data['subtotal'] + $product['ammount'];
        }
        $data['ammountDue'] = $data['subtotal'];

        if ($data['taxId']){
            $tax = $this->tax->getTaxDetail($data['taxId']);
            $data['totalTax'] = $data['subtotal'] * $tax->percentage/100;
            $data['ammountDue'] = $data['ammountDue'] + $data['totalTax'];
        }else{
            $data['totalTax'] = 0;
        }

        $invoice = Invoice::create($data);

        foreach ($data['products'] as $product){
            $invoice->products()->attach($product['productId'], [
                'qty' => $product['qty'],
                'ammount' => $product['ammount']
            ]);

            $productDetail = $this->product->findProductById($product['productId']);
            if ($productDetail->priceOption == 'subscription'){
                $subscription = $this->storeSubscription($data['receiverUserId'], $product['ammount'], $product['productId']);
                $invoice->subscriptions()->attach($subscription->id);
            }
        }

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
                'gopay', 'bank_transfer'
            ],
            'vtweb' => []
        ];

        try {
            $snaptoken = Snap::getSnapToken($midtrans);
            $invoice['snapToken'] = $snaptoken;
            $invoice->save();

//            Mail::to($data['email'])->send(new PayBookingTrfMail($booking));

//            return redirect()->route('pay-booking', $booking->transaction_code);
        }
        catch (Exception $e){
            echo $e->getMessage();
        }

        return $invoice;
    }

    public function calculateProductAmount(int $qty, $productId){
        $product = $this->product->findProductById($productId);
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
            $tax = $this->tax->getTaxDetail($data['taxId']);
            $data['totalTax'] = $data['subtotal'] * $tax->percentage;
            $data['ammountDue'] = $data['ammountDue'] + $data['totalTax'];
        }

        return $data;
    }

    public function storeSubscription($userId, $ammount, $productId){
        $data = [];
        $data['startDate'] = Carbon::now();
        $data['ammountDue'] = $ammount;
        $data['status'] = 'scheduled';
        $data['userId'] = $userId;
        $data['productId'] = $productId;

        $product = $this->product->findProductById($productId);

        if ($product->subscriptionCycle == 'monthly'){
            $data['cycle'] =  'monthly';
            $data['nextDueDate'] = Carbon::now()->addMonthsNoOverflow(1);
        } elseif ($product->subscriptionCycle == 'quarterly'){
            $data['cycle'] =  'quarterly';
            $data['nextDueDate'] = Carbon::now()->addMonthsNoOverflow(3);
        } elseif ($product->subscriptionCycle == 'semianually'){
            $data['cycle'] =  'semianually';
            $data['nextDueDate'] = Carbon::now()->addMonthsNoOverflow(6);
        } elseif ($product->subscriptionCycle == 'anually'){
            $data['cycle'] =  'anually';
            $data['nextDueDate'] = Carbon::now()->addMonthsNoOverflow(12);
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

    public function update(array $data, Invoice $invoice)
    {
        $data['subtotal'] = 0;
        foreach ($data['products'] as $product) {
            $data['subtotal'] = $data['subtotal'] + $product['ammount'];
        }
        $data['ammountDue'] = $data['subtotal'];

        if ($data['taxId']){
            $tax = $this->tax->getTaxDetail($data['taxId']);
            $data['totalTax'] = $data['subtotal'] * $tax->percentage/100;
            $data['ammountDue'] = $data['ammountDue'] + $data['totalTax'];
        }else{
            $data['totalTax'] = 0;
            $data['ammountDue'] = $data['ammountDue'] - $invoice->totalTax;
        }

        $invoice->products()->sync($data['products']);

        $invoiceSubscriptions = $invoice->subscriptions()->get();

        foreach ($invoiceSubscriptions as $subscription){
            if (!in_array($subscription->id, $data['products'])){
                $invoice->subscriptions->detach($subscription->id);
                Subscription::destroy($subscription->id);
            }
        }

        foreach ($data['products'] as $product){
            $productDetail = $this->product->findProductById($product['productId']);

            if ($productDetail->priceOption == 'subscription'){
                if ($this->checkSubscriptionIsExist($data['productId'], $data['receiverUserId']) == null){
                    $subscription = $this->storeSubscription($data['receiverUserId'], $product['ammount'], $product['productId']);
                    $invoice->subscriptions->attach($subscription->id);
                }
            }
        }
        return $invoice->update($data);
    }

    public function checkSubscriptionIsExist($productId, $userId){
        return Subscription::where('productId', $productId)
            ->where('userId', $userId)
            ->exist();
    }

    public function paid(Invoice $invoice)
    {
        return $invoice->update(['status' => 'paid']);
    }

    public function uncollectible(Invoice $invoice)
    {
        return $invoice->update(['status' => 'uncollectible']);
    }

    public function destroy(Invoice $invoice)
    {
        return $invoice->delete();
    }
}
