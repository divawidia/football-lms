<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function standings(): HasMany
    {
        return $this->hasMany(LeagueStanding::class, 'competitionId');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(MatchModel::class, 'competitionId');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }
}
