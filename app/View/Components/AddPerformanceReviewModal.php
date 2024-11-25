<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AddPerformanceReviewModal extends Component
{
    public $routeCreate;
    /**
     * Create a new component instance.
     */
    public function __construct($routeCreate)
    {
        $this->routeCreate = $routeCreate;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.add-performance-review-modal');
    }
}
