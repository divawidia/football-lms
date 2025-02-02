<?php

namespace App\View\Components\modal\Trainings;

use App\Models\Training;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EditCoachAttendance extends Component
{
    public Training $training;
    /**
     * Create a new component instance.
     */
    public function __construct(Training $training)
    {
        $this->training = $training;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.trainings.edit-coach-attendance');
    }
}
