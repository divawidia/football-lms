<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Veelasky\LaravelHashId\Eloquent\HashableId;

class Invoice extends Model
{
    use HasFactory, SoftDeletes, HashableId;

    protected $fillable = [
        'creatorUserId',
        'receiverUserId',
        'academyId',
        'taxId',
        'invoiceNumber',
        'dueDate',
        'ammountDue',
        'totalTax',
        'subtotal',
        'snapToken',
        'status',
        'paymentMethod',
        'isReminderNotified',
    ];

    public function creatorUser()
    {
        return $this->belongsTo(User::class, 'creatorUserId');
    }
    public function receiverUser()
    {
        return $this->belongsTo(User::class, 'receiverUserId');
    }
    public function academy()
    {
        return $this->belongsTo(Academy::class, 'academyId');
    }
    public function tax()
    {
        return $this->belongsTo(Tax::class, 'taxId');
    }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_invoices', 'invoiceId', 'productId')
            ->withPivot('qty', 'ammount')->withTimestamps();
    }
    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class, 'invoice_subscriptions', 'invoiceId', 'subscriptionId')
            ->withTimestamps();
    }
}
