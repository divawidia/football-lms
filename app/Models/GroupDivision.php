<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function teams(){
        return $this->belongsToMany(Team::class, 'competition_team', 'divisionId', 'teamId');
    }
}
