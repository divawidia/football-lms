<?php

namespace App\View\Components\modal;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AddPlayersToTeam extends Component
{
    public $players;
    public $route;
    /**
     * Create a new component instance.
     */
    public function __construct($players, $route)
    {
        $this->players = $players;
        $this->route = $route;

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.add-players-to-team');
    }
}
