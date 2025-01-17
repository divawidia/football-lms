<?php

namespace App\View\Components\Tables;

use App\Models\Match;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PlayerPerformanceReviewEvent extends Component
{
    public $tableId;
    public Match $eventSchedule;
    public $teamId;

    /**
     * Create a new component instance.
     */
    public function __construct($tableId, Match $eventSchedule, $teamId = null)
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
        return view('components.tables.player-performance-review-event');
    }
}
