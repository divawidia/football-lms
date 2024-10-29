<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PlayerPerformanceReviewEventTable extends Component
{
    public $tableId;
    public $route;

    /**
     * Create a new component instance.
     */
    public function __construct($tableId, $route)
    {
        $this->tableId = $tableId;
        $this->route = $route;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.tables.player-performance-review-event-table');
    }
}
