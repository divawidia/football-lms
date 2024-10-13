<?php

namespace App\Services\Coach;

use App\Models\Team;
use App\Services\Service;

class CoachService extends Service
{
    public function managedTeams($coach){
        return Team::with('coaches')
            ->whereHas('coaches', function($q) use ($coach) {
                $q->where('coachId', $coach->id);
            })->get();
    }
}
