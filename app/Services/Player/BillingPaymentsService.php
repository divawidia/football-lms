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
    public function __construct(
        User $user)
    {
        Config::$serverKey    = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized  = config('services.midtrans.isSanitized');
        Config::$is3ds        = config('services.midtrans.is3ds');

        $this->user = $user;
    }
    public function index()
    {
        $data = $this->user->invoices;
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                return
                    '<a class="btn btn-sm btn-outline-secondary" href="' . route('invoices.show', $item->id) . '" data-toggle="tooltip" data-placement="bottom" title="Show subscription detail">
                        <span class="material-icons">
                            visibility
                        </span>
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

    public function show(Invoice $invoice)
    {
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

    public function paid(Invoice $invoice)
    {
        return $invoice->update(['status' => 'Paid']);
    }
}
