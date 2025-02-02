<?php

namespace App\View\Components\Tables;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PlayerSkillEvent extends Component
{
    public $tableId;
    public $teamId;
    public $route;

    /**
     * Create a new component instance.
     */
    public function __construct($route, $tableId = 'player-skill-stats-table', $teamId = null)
    {
        $this->tableId = $tableId;
        $this->route = $route;
        $this->teamId = $teamId;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.tables.player-skill-event');
    }
}
