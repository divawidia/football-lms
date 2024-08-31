<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'teamName',
        'ageGroup',
        'logo',
        'status',
        'academyId',
    ];

    public function academy()
    {
        return $this->belongsTo(Academy::class, 'academyId');
    }
    public function teamMatchStats()
    {
        return $this->hasMany(TeamMatchStats::class, 'teamId');
    }
    public function players()
    {
        return $this->belongsToMany(Player::class, 'player_teams', 'teamId', 'playerId')->withTimestamps();
    }
    public function coaches()
    {
        return $this->belongsToMany(Coach::class, 'coach_team', 'teamId', 'coachId')->withTimestamps();
    }
}
