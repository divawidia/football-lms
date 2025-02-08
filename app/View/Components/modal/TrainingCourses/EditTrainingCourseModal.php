<?php

namespace App\View\Components\modal\TrainingCourses;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EditTrainingCourseModal extends Component
{
    public $routeEdit;
    public $routeUpdate;
    /**
     * Create a new component instance.
     */
    public function __construct($routeEdit, $routeUpdate)
    {
        $this->routeEdit = $routeEdit;
        $this->routeUpdate = $routeUpdate;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.training-courses.edit-training-course-modal');
    }
}
