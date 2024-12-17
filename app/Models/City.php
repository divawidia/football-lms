<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'country_id',
        'state_id',
        'name',
        'country_code',
    ];
}
