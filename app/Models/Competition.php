<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Veelasky\LaravelHashId\Eloquent\HashableId;

class Competition extends Model
{
    use HasFactory, HashableId;

    protected $fillable = [
        'name',
        'type',
        'logo',
        'startDate',
        'endDate',
        'location',
        'isInternal',
        'status',
        'userId'
    ];

    public function standings()
    {
        return $this->hasMany(LeagueStanding::class, 'competitionId');
    }
    public function groups(){
        return $this->hasMany(GroupDivision::class, 'competitionId');
    }

    public function matches(){
        return $this->hasMany(Match::class, 'competitionId');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}
