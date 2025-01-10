<?php

namespace App\View\Components\Tables;

use App\Models\EventSchedule;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PlayerSkillEvent extends Component
{
    public $tableId;
    public EventSchedule $eventSchedule;
    public $teamId;

    /**
     * Create a new component instance.
     */
    public function __construct($tableId, EventSchedule $eventSchedule, $teamId = null)
    {
        $this->tableId = $tableId;
        $this->eventSchedule = $eventSchedule;
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
