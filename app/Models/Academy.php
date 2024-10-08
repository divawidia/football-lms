<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nnjeim\World\Models\City;
use Nnjeim\World\Models\Country;
use Nnjeim\World\Models\State;

class Academy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'email',
        'phoneNumber',
        'academyName',
        'address',
        'state_id',
        'city_id',
        'country_id',
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
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
