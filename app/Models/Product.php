<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Veelasky\LaravelHashId\Eloquent\HashableId;

class Product extends Model
{
    use HasFactory, HashableId;

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

    public function findProductById($productId){
        return $this->findOrFail($productId);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'categoryId');
    }
    public function subscritions()
    {
        return $this->hasMany(Subscription::class, 'productId');
    }
    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'productId', 'invoiceId')
            ->withPivot('qty','ammount')->withTimestamps();
    }
}
