<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoachSpecialization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function coaches()
    {
        return $this->hasMany(Coach::class, 'specialization');
    }
}
