<?php

namespace App\View\Components\modal\PlayersCoaches;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AddTeams extends Component
{
    public $route;
    public $teams;
    /**
     * Create a new component instance.
     */
    public function __construct($route, $teams)
    {
        $this->route = $route;
        $this->teams = $teams;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.players-coaches.add-teams');
    }
}
