<?php

namespace App\View\Components\modal;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AddCoachesToTeam extends Component
{
    public $coaches;
    public $route;
    /**
     * Create a new component instance.
     */
    public function __construct($coaches, $route)
    {
        $this->coaches = $coaches;
        $this->route = $route;

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.add-coaches-to-team');
    }
}
