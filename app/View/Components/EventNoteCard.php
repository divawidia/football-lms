<?php

namespace App\View\Components;

use App\Models\Match;
use App\Models\ScheduleNote;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EventNoteCard extends Component
{
    public ScheduleNote $note;
    public Match $schedule;

    /**
     * Create a new component instance.
     */
    public function __construct(ScheduleNote $note, Match $schedule)
    {
        $this->note = $note;
        $this->schedule = $schedule;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cards.event-note');
    }
}
