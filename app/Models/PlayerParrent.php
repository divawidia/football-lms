<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Veelasky\LaravelHashId\Eloquent\HashableId;

class PlayerParrent extends Model
{
    use HasFactory, HashableId;

    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'phoneNumber',
        'relations',
        'playerId',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class, 'playerId');
    }
}
