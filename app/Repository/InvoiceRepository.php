<?php

namespace App\Repository;

use App\Models\Invoice;
class InvoiceRepository
{
    protected Invoice $invoice;
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
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
