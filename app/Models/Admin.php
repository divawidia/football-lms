<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Veelasky\LaravelHashId\Eloquent\HashableId;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HashableId;

    protected $fillable = [
        'position',
        'hireDate',
        'userId'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }
    public function invoice()
    {
        return $this->hasMany(Invoice::class, 'adminId', 'id');
    }


}
