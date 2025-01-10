<?php

namespace App\View\Components\Cards;

use App\Models\EventSchedule;
use App\Models\ScheduleNote;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EventNote extends Component
{
    public ScheduleNote $note;
    public EventSchedule $schedule;

    /**
     * Create a new component instance.
     */
    public function __construct(ScheduleNote $note, EventSchedule $schedule)
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
