<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'userId',
        'cycle',
        'startDate',
        'nextDueDate',
        'ammountDue',
        'status',
    ];

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_subscriptions', 'subscriptionId', 'invoiceId')
            ->withTimestamps();
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }
}
