<?php

namespace App\View\Components\Modal;

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
        return view('components.modal.edit-training-course-lesson-modal');
    }
}
