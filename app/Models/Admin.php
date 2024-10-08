<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

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
