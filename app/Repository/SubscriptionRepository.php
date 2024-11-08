<?php

namespace App\Repository;

use App\Models\Subscription;

class SubscriptionRepository
{
    protected Subscription $subscription;
    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    public function getAll()
    {
        return $this->subscription->with('user')->latest();
    }

    public function find($id)
    {
        return $this->subscription->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->subscription->create($data);
    }

    public function update(array $data)
    {
        return $this->subscription->update($data);
    }

    public function delete()
    {
        return $this->subscription->delete();
    }
}
