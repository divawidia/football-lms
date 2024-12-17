<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'country_id',
        'name',
        'country_code',
    ];
}
