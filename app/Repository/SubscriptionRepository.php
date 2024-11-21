<?php

namespace App\Repository;

use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
            ->where('nextDueDate', '>=', Carbon::now())
            ->selectRaw("
                SUM(CASE
                    WHEN cycle = 'monthly' THEN ammountDue
                    WHEN cycle = 'quarterly' THEN ammountDue / 4
                    WHEN cycle = 'anually' THEN ammountDue / 12
                    ELSE 0
                END) as mrr,
                SUM(CASE
                    WHEN cycle = 'monthly' THEN ammountDue * 12
                    WHEN cycle = 'quarterly' THEN ammountDue * 4
                    WHEN cycle = 'anually' THEN ammountDue
                    ELSE 0
                END) as yrr,
                SUM(CASE
                    WHEN cycle = 'monthly' THEN ammountDue * 3
                    WHEN cycle = 'quarterly' THEN ammountDue
                    WHEN cycle = 'anually' THEN ammountDue / 4
                    ELSE 0
                END) as qrr
            ")
            ->first();
    }

    public function playerSubscriptionTrend($status, $selectDate, $startDate = null, $endDate = null)
    {
        $query = $this->subscription->select($selectDate, DB::raw('COUNT(ID) AS count'))->where('status', $status);
        if ($startDate != null && $endDate != null) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        return $query->groupBy('date')->get();
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
