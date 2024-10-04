<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'userId',
        'categoryId',
        'productName',
        'price',
        'description',
        'priceOption',
        'subscriptionCycle',
        'status',
    ];

    public function getAllProducts(){
        return $this->with('category')->get();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
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
