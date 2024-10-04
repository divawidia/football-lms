<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Subscription;
use App\Models\Tax;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class InvoiceService extends Service
{
    public function index()
    {
        $data = Invoice::with('player.user')->latest();
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                $paidButton =
                    '<form action="' . route('invoices.paid', $item->id) . '" method="POST">
                        ' . method_field("PATCH") . '
                        ' . csrf_field() . '
                        <button type="submit" class="dropdown-item">
                            <span class="material-icons text-success">check_circle</span>
                            Mark as Paid
                        </button>
                    </form>';
                $uncollectibleButton =
                    '<form action="' . route('invoices.uncollectible', $item->id) . '" method="POST">
                        ' . method_field("PATCH") . '
                        ' . csrf_field() . '
                        <button type="submit" class="dropdown-item">
                            <span class="material-icons text-danger">check_circle</span>
                            Mark as Uncollectible
                        </button>
                    </form>';

                if ($item->status == 'open') {
                    $statusButton = [$paidButton, $uncollectibleButton];
                } elseif ($item->status == 'paid') {
                    $statusButton = $uncollectibleButton;
                } elseif ($item->status == 'uncollectible') {
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
                            <button class="dropdown-item edit" id="' . $item->id . '" type="button">
                                <span class="material-icons">edit</span>
                                Edit Invoice
                             </button>
                             ' . $statusButton . '
                            <button type="button" class="btn btn-sm btn-outline-secondary deleteTax" id="' . $item->id . '">
                                <span class="material-icons">delete</span>
                                Delete Invoice
                            </button>
                        </div>';
            })
            ->editColumn('createdBy', function ($item) {
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
                                            <small class="js-lists-values-email text-50">' . $item->user->admin->position . '</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            })
            ->editColumn('ammount', function ($item) {
                return $this->priceFormat($item->ammountDue);
            })
            ->editColumn('dueDate', function ($item) {
                return $this->convertTimestamp($item->dueDate);
            })
            ->editColumn('createdAt', function ($item) {
                return $this->convertTimestamp($item->created_at);
            })
            ->editColumn('status', function ($item) {
                if ($item->status == 'open') {
                    $badge = '<span class="badge badge-pill badge-info">Open</span>';
                } elseif ($item->status == 'paid') {
                    $badge = '<span class="badge badge-pill badge-success">Paid</span>';
                } elseif ($item->status == 'pastDue') {
                    $badge = '<span class="badge badge-pill badge-warning">Past Due</span>';
                } elseif ($item->status == 'uncollectible') {
                    $badge = '<span class="badge badge-pill badge-warning">Uncollectable</span>';
                }
                return $badge;
            })
            ->rawColumns(['action', 'createdBy', 'ammount','dueDate', 'createdAt', 'status'])
            ->addIndexColumn()
            ->make();
    }

    public function store(array $data, $userId, $academyId)
    {
        $data['userId'] = $userId;
        $data['academyId'] = $academyId;
        $data['invoiceNumber'] = $this->generateInvoiceNumber();
        $data['status'] = 'open';
        $data['subtotal'] = 0;
        $data['sentDate'] = Carbon::now();

        foreach ($data['products'] as $product) {
            $data['subtotal'] = $data['subtotal'] + $product['ammount'];
        }
        $data['ammountDue'] = $data['subtotal'];

        if ($data['taxId']){
            $tax = $this->getTaxDetail($data['taxId']);
            $data['totalTax'] = $data['subtotal'] * $tax->percentage;
            $data['ammountDue'] = $data['ammountDue'] + $data['totalTax'];
        }

        $invoice = Invoice::create($data);

        foreach ($data['products'] as $product){
            $invoice->product()->attach($product['productId'], [
                'qty' => $product['qty'],
                'ammount' => $product['ammount']
            ]);

            $productDetail = $this->getProductDetail($product['productId']);
            if ($productDetail->priceOption == 'subscription'){
                $this->storeSubscription($data['playerId'], $product['ammount'], $product['productId']);
            }
        }

        return $invoice;
    }

    public function getProductDetail($productId){
        return Product::findOrFail($productId);
    }

    public function getTaxDetail($taxId){
        return Tax::findOrFail($taxId);
    }

    public function storeSubscription($playerId, $ammount, $productId){
        $data = [];
        $data['startDate'] = Carbon::now();
        $data['ammountDue'] = $ammount;
        $data['status'] = 'scheduled';
        $data['playerId'] = $playerId;

        $product = $this->getProductDetail($productId);

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

    public function update(array $data, Invoice $invoice)
    {
        return $invoice->update($data);
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
