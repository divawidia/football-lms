<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MatchScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'playerId',
        'assistPlayerId',
        'matchId',
        'minuteScored',
        'isOwnGoal',
        'teamId'
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'playerId', 'id');
    }
    public function assistPlayer(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'assistPlayerId', 'id');
    }
    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchModel::class, 'matchId', 'id');
    }
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'teamId', 'id');
    }
}
