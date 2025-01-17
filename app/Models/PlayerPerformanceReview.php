<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Veelasky\LaravelHashId\Eloquent\HashableId;

class PlayerPerformanceReview extends Model
{
    use HasFactory, HashableId;

    protected $fillable = [
        'playerId',
        'coachId',
        'eventId',
        'performanceReview',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class, 'playerId', 'id');
    }
    public function coach()
    {
        return $this->belongsTo(Coach::class, 'coachId', 'id');
    }
    public function event()
    {
        return $this->belongsTo(Match::class, 'eventId', 'id');
    }
}
