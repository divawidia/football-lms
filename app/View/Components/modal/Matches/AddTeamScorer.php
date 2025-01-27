<?php

namespace App\View\Components\modal\Matches;

use App\Models\Match;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AddTeamScorer extends Component
{
    public Match $eventSchedule;
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
        return view('components.modal.matches.add-team-scorer');
    }
}
