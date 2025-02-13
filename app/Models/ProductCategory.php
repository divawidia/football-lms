<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Veelasky\LaravelHashId\Eloquent\HashableId;

class ProductCategory extends Model
{
    use HasFactory, HashableId;

    protected $fillable = [
        'userId',
        'categoryName',
        'description',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
    public function products()
    {
        return $this->hasMany(Product::class, 'categoryId');
    }
}
