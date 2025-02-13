<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'categoryId');
    }
    public function subscritions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'productId');
    }
}
