<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Veelasky\LaravelHashId\Eloquent\HashableId;

class Tax extends Model
{
    use HasFactory, HashableId;

    protected $fillable = [
        'taxName',
        'percentage',
        'description',
        'status',
        'userId'
    ];

    public function invoice(): HasMany
    {
        return $this->hasMany(Invoice::class, 'taxId');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'taxId');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }
}
