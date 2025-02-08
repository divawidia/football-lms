<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MatchTables extends Component
{
    public $route;
    public $tableId;
    /**
     * Create a new component instance.
     */
    public function __construct($route, $tableId)
    {
        $this->route = $route;
        $this->tableId = $tableId;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.tables.match-tables');
    }
}
