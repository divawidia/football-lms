<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PlayerSKillStatsCard extends Component
{
    public $allSkills;
    /**
     * Create a new component instance.
     */
    public function __construct($allSkills)
    {
        $this->allSkills = $allSkills;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cards.player-s-kill-stats-card');
    }
}
