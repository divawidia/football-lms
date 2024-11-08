<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\Product;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

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

    public function getByPriceOption($priceOption)
    {
        return $this->product->where('priceOption', $priceOption)->get();
    }

    public function getAvailablePlayerSubscriptionProduct($userId)
    {
        return Product::with('subscritions')->where('priceOption', '=', 'subscription')
            ->whereDoesntHave('subscritions', function (Builder $query) use ($userId) {
                $query->where('userId', $userId);
            })->get();
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
