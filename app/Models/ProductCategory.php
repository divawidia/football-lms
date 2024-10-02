<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'adminId',
        'categoryName',
        'description',
        'status',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'adminId');
    }
    public function products()
    {
        return $this->hasMany(Product::class, 'categoryId');
    }
}
