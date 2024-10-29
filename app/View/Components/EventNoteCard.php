<?php

namespace App\View\Components;

use App\Models\ScheduleNote;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EventNoteCard extends Component
{
    public ScheduleNote $note;
    public $deleteRoute;

    /**
     * Create a new component instance.
     */
    public function __construct(ScheduleNote $note, $deleteRoute)
    {
        $this->note = $note;
        $this->deleteRoute = $deleteRoute;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cards.event-note-card');
    }
}
