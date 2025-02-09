<?php

namespace App\View\Components\Tables;

use App\Models\MatchModel;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PlayerLeaderboard extends Component
{
    public $playersLeaderboardRoute;

    /**
     * Create a new component instance.
     */
    public function __construct($playersLeaderboardRoute)
    {
        $this->playersLeaderboardRoute = $playersLeaderboardRoute;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.tables.player-leaderboard');
    }
}
