<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EditPlayerAttendanceModal extends Component
{
    public $routeGet;
    public $routeUpdate;
    /**
     * Create a new component instance.
     */
    public function __construct($routeGet, $routeUpdate)
    {
        $this->routeGet = $routeGet;
        $this->routeUpdate = $routeUpdate;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.edit-player-attendance-modal');
    }
}
