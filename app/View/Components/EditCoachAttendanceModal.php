<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EditCoachAttendanceModal extends Component
{
    public $routeGet;
    public $routeUpdate;
    public $routeAfterProcess;
    /**
     * Create a new component instance.
     */
    public function __construct($routeGet, $routeUpdate, $routeAfterProcess)
    {
        $this->routeGet = $routeGet;
        $this->routeUpdate = $routeUpdate;
        $this->routeAfterProcess = $routeAfterProcess;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.edit-coach-attendance-modal');
    }
}
