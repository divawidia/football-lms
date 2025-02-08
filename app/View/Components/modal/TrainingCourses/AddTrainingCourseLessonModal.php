<?php

namespace App\View\Components\modal\TrainingCourses;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AddTrainingCourseLessonModal extends Component
{
    public $routeStore;
    /**
     * Create a new component instance.
     */
    public function __construct($routeStore)
    {
        $this->routeStore = $routeStore;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.training-courses.add-training-course-lesson-modal');
    }
}
