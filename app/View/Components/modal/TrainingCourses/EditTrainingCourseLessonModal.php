<?php

namespace App\View\Components\modal\TrainingCourses;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EditTrainingCourseLessonModal extends Component
{
    public $trainingVideo;
    /**
     * Create a new component instance.
     */
    public function __construct($trainingVideo)
    {
        $this->trainingVideo = $trainingVideo;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.training-courses.edit-training-course-lesson-modal');
    }
}
