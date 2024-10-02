<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'adminId',
        'categoryId',
        'productName',
        'price',
        'description',
        'priceOption',
        'subscriptionCycle',
        'status',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'adminId');
    }
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'categoryId');
    }
    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'productId', 'invoiceId')
            ->withPivot('qty','ammount')->withTimestamps();
    }
}
