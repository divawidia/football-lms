<?php

namespace App\View\Components\modal\Matches;

use App\Models\EventSchedule;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AddTeamOwnGoal extends Component
{
    public EventSchedule $eventSchedule;
    /**
     * Create a new component instance.
     */
    public function __construct($eventSchedule)
    {
        $this->eventSchedule = $eventSchedule;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.matches.add-team-own-goal');
    }
}
