<?php

namespace App\Services\Coach;

use App\Models\Team;

class CoachService
{
    public function managedTeams($coachId){
        return Team::with('coaches')
            ->whereHas('coaches', function($q) use ($coachId) {
                $q->where('coachId', $coachId->id);
            })->get();
    }
}
