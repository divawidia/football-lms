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

    public function recurringRevenue()
    {
        return $this->subscription->where('status', 'Scheduled')
            ->where(function($query) {
                $query->whereNull('nextDueDate')
                ->orWhere('nextDueDate', '>=', now());
            })
            ->selectRaw("
                SUM(CASE
                    WHEN billing_cycle = 'monthly' THEN price
                    WHEN billing_cycle = 'quarterly' THEN price / 4
                    WHEN billing_cycle = 'annual' THEN price / 12
                    ELSE 0
                END) as mrr,
                SUM(CASE
                    WHEN billing_cycle = 'monthly' THEN price * 12
                    WHEN billing_cycle = 'quarterly' THEN price * 4
                    WHEN billing_cycle = 'annual' THEN price
                    ELSE 0
                END) as yrr,
                SUM(CASE
                    WHEN billing_cycle = 'monthly' THEN price * 3
                    WHEN billing_cycle = 'quarterly' THEN price
                    WHEN billing_cycle = 'annual' THEN price / 4
                    ELSE 0
                END) as qrr
            ")
            ->first();
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
