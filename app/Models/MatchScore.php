<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MatchScore extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'playerId',
        'assistPlayerId',
        'eventId',
        'minuteScored',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class, 'playerId', 'id');
    }
    public function assistPlayer()
    {
        return $this->belongsTo(Player::class, 'assistPlayerId', 'id');
    }
    public function event()
    {
        return $this->belongsTo(EventSchedule::class, 'eventId', 'id');
    }
}
