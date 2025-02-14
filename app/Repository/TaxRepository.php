<?php

namespace App\Repository;

use Illuminate\Database\Eloquent\Collection;

class TaxRepository
{
    protected Tax $tax;
    public function __construct(Tax $tax)
    {
        $this->tax = $tax;
    }

    public function getAll($withRelations = [], $status = null): Collection|array
    {
        return $this->tax->with($withRelations)
            ->when($status, fn($query) => $query->where('status', $status))
            ->get();
    }

    public function find($id)
    {
        return $this->tax->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->tax->create($data);
    }

    public function update(array $data): bool
    {
        return $this->tax->update($data);
    }

    public function delete(): ?bool
    {
        return $this->tax->delete();
    }
}
