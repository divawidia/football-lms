<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SkillAssessmentsModal extends Component
{
    public $routeAfterProcess;
    /**
     * Create a new component instance.
     */
    public function __construct($routeAfterProcess)
    {
        $this->routeAfterProcess = $routeAfterProcess;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.skill-assessments-modal');
    }
}
