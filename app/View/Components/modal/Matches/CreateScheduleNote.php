<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CreateScheduleNoteModal extends Component
{
    public $routeCreate;
    public $eventName;
    /**
     * Create a new component instance.
     */
    public function __construct($routeCreate, $eventName)
    {
        $this->routeCreate = $routeCreate;
        $this->eventName = $eventName;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.create-schedule-note-modal');
    }
}
