<?php

namespace App\Services\Coach;

use App\Models\Team;

class CoachService
{
    public function managedTeams($coach){
        return Team::with('coaches')
            ->whereHas('coaches', function($q) use ($coach) {
                $q->where('coachId', $coach->id);
            })->get();
    }
}
