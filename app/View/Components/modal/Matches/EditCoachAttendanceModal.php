<?php

namespace App\View\Components\modal\Matches;

use App\Models\EventSchedule;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EditCoachAttendanceModal extends Component
{
    public EventSchedule $schedule;
    /**
     * Create a new component instance.
     */
    public function __construct($schedule)
    {
        $this->schedule = $schedule;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.matches.edit-coach-attendance');
    }
}
