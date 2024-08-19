<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'firstName',
        'lastName',
        'position',
        'skill',
        'strongFoot',
        'height',
        'weight',
        'joinDate',
        'status',
        'userId'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}
