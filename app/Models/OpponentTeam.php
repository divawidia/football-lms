<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpponentTeam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'teamName',
        'ageGroup',
        'division',
        'logo',
        'status',
        'coachName',
        'academyName'
    ];

    public function event()
    {
        return $this->hasMany(EventSchedule::class, 'opponentTeamId');
    }
}
