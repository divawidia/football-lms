<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GroupDivision extends Model
{
    use HasFactory;

    protected $fillable = [
        'competitionId',
        'groupName',
    ];

    public function competition(){
        return $this->belongsTo(Competition::class, 'competitionId');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'competition_team', 'divisionId', 'teamId')
            ->withPivot(
                'matchPlayed',
                'won',
                'drawn',
                'lost',
                'goalsFor',
                'goalsAgaints',
                'goalsDifference',
                'points',
                'redCards',
                'yellowCards',
                'competitionResult',
            )->withTimestamps();
    }
}
