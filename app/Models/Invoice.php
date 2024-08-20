<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'playerId',
        'adminId',
        'academyId',
        'taxId',
        'invoiceNumber',
        'dueDate',
        'ammountDue',
        'sentDate',
        'totalTax',
        'subtotal',
        'paymentURL',
        'status',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class, 'playerId');
    }
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'adminId');
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
