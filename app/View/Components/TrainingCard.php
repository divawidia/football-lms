<?php

namespace App\View\Components;

use App\Models\EventSchedule;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TrainingCard extends Component
{
    public EventSchedule $training;
    /**
     * Create a new component instance.
     */
    public function __construct(EventSchedule $training)
    {
        $this->training = $training;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cards.training-card');
    }
}
