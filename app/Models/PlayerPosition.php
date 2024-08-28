<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category'
    ];

    public function players()
    {
        return $this->hasMany(Player::class);
    }
}
