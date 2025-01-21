<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Veelasky\LaravelHashId\Eloquent\HashableId;

class PlayerPerformanceReview extends Model
{
    use HasFactory, HashableId;

    protected $fillable = [
        'playerId',
        'coachId',
        'matchId',
        'performanceReview',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'playerId', 'id');
    }
    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class, 'coachId', 'id');
    }
    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchModel::class, 'matchId', 'id');
    }
}
