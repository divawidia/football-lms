<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EditScheduleNoteModal extends Component
{
    public $routeEdit;
    public $routeUpdate;
    public $eventName;
    public $routeAfterProcess;
    /**
     * Create a new component instance.
     */
    public function __construct($routeEdit, $routeUpdate, $eventName, $routeAfterProcess)
    {
        $this->routeEdit = $routeEdit;
        $this->routeUpdate = $routeUpdate;
        $this->eventName = $eventName;
        $this->routeAfterProcess = $routeAfterProcess;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.edit-schedule-note-modal');
    }
}
