<?php

namespace App\Repository;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    protected Product $product;
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function getAll($withRelations = [], $priceOption = null, $status = null): Collection
    {
        return $this->product->with($withRelations)
            ->when($priceOption, fn($query) => $query->where('priceOption', $priceOption))
            ->when($status, fn($query) => $query->where('status', $status))
            ->get();
    }

    public function getAvailablePlayerSubscriptionProduct($userId): Collection|array
    {
        return $this->product->with('subscritions')
            ->where('priceOption', 'subscription')
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

    public function update(array $data, Product $product): bool
    {
        return $product->update($data);
    }

    public function delete(Product $product): ?bool
    {
        return $product->delete();
    }
}
