<?php

namespace App\View\Components\Tables;

use App\Models\Match;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TeamLeaderboard extends Component
{
    public $teamsLeaderboardRoute;

    /**
     * Create a new component instance.
     */
    public function __construct($teamsLeaderboardRoute)
    {
        $this->teamsLeaderboardRoute = $teamsLeaderboardRoute;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.tables.team-leaderboard');
    }
}
