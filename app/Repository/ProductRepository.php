<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\Product;
use App\Models\Tax;
use App\Models\User;

class ProductRepository
{
    protected Product $product;
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function getAll()
    {
        return $this->product->all();
    }

    public function find($id)
    {
        return $this->product->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->product->create($data);
    }

    public function update(array $data, Product $product)
    {
        return $product->update($data);
    }

    public function delete(Product $product)
    {
        return $product->delete();
    }
}
