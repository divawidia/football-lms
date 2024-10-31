<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PlayerTrainingHistoriesTable extends Component
{
    public $tableId;
    public $tableRoute;
    /**
     * Create a new component instance.
     */
    public function __construct($tableId, $tableRoute)
    {
        $this->tableId = $tableId;
        $this->tableRoute = $tableRoute;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.tables.player-training-histories-table');
    }
}
