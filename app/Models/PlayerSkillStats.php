<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Veelasky\LaravelHashId\Eloquent\HashableId;

class PlayerSkillStats extends Model
{
    use HasFactory, HashableId;

    protected $table = 'player_skills_stats';
    protected $fillable = [
        'playerId',
        'coachId',
        'eventId',
        'controlling',
        'recieving',
        'dribbling',
        'passing',
        'shooting',
        'crossing',
        'turning',
        'ballHandling',
        'powerKicking',
        'goalKeeping',
        'offensivePlay',
        'defensivePlay'
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
