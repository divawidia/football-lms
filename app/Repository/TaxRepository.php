<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\Tax;
use App\Models\User;

class TaxRepository
{
    protected Tax $tax;
    public function __construct(Tax $tax)
    {
        $this->tax = $tax;
    }

    public function getAll()
    {
        return $this->tax->all();
    }

    public function find($id)
    {
        return $this->tax->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->tax->create($data);
    }

    public function update(array $data)
    {
        return $this->tax->update($data);
    }

    public function delete()
    {
        return $this->tax->delete();
    }
}
