<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Academy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'email',
        'phoneNumber',
        'academyName',
        'address',
        'state',
        'city',
        'country',
        'zipCode',
        'directorName',
        'status',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'academyId', 'id');
    }

    public function teams()
    {
        return $this->hasMany(Team::class, 'academyId', 'id');
    }
    public function invoice()
    {
        return $this->hasMany(Invoice::class, 'academyId', 'id');
    }
}
