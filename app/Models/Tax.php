<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
{
    use HasFactory;

    protected $fillable = [
        'taxName',
        'percentage',
        'description',
        'status',
        'userId'
    ];

    public function invoice()
    {
        return $this->hasMany(Invoice::class, 'taxId');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'taxId');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}
