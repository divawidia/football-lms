<?php

namespace App\View\Components\Charts;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PlayerSkillHistoryChart extends Component
{
    public $player;
    /**
     * Create a new component instance.
     */
    public function __construct($player)
    {
        $this->player = $player;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.charts.player-skill-history-chart');
    }
}
