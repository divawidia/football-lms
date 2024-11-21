<?php

namespace App\Repository;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class InvoiceRepository
{
    protected Invoice $invoice;
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function calculateInvoiceByStatus($status, $startDate = null, $endDate = null, $countInvoice = false, $sumAmount = false)
    {
        $query = $this->invoice->where('status', $status);
        if ($startDate != null && $endDate != null) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        if ($countInvoice) {
            return $query->count();
        } elseif ($sumAmount) {
            return $query->sum('ammountDue');
        }
    }

    public function revenue($selectDate, $startDate = null, $endDate = null)
    {
        $results = $this->invoice->select($selectDate, DB::raw('SUM(ammountDue) AS total_ammount'))->where('status', '=', 'Paid');

        if ($startDate != null && $endDate != null) {
            $results->whereBetween('created_at', [$startDate, $endDate]);
        }
        $results->groupBy('date');

        if ($selectDate == DB::raw('MONTHNAME(created_at) as date')) {
            $results->orderByRaw("FIELD(date, 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December')");
        } elseif($selectDate == DB::raw('DAYNAME(created_at) as date')) {
            $results->orderByRaw("FIELD(date, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')");
        } else {
            $results->orderBy('date');
        }
            return $results->get();
    }

    public function invoiceStatus()
    {
        return $this->invoice->selectRaw('status, COUNT(status) as count')->groupBy('status')->get();
    }

    public function paymentType()
    {
        return $this->invoice->selectRaw('paymentMethod, COUNT(paymentMethod) as count')->groupBy('paymentMethod')->get();
    }

    public function getAll()
    {
        return $this->invoice->with('receiverUser', 'creatorUser')->latest();
    }

    public function find($id)
    {
        return $this->invoice->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->invoice->create($data);
    }

    public function update(array $data)
    {
        return $this->invoice->update($data);
    }

    public function delete()
    {
        return $this->invoice->delete();
    }
}
