<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DeleteDataConfirmation extends Component
{
    public $deleteBtnClass;
    public $destroyRoute;
    public $routeAfterDelete;
    public $confirmationText;
    public $successText;
    public $errorText;
    /**
     * Create a new component instance.
     */
    public function __construct($deleteBtnClass, $destroyRoute, $routeAfterDelete, $confirmationText, $successText, $errorText)
    {
        $this->deleteBtnClass = $deleteBtnClass;
        $this->destroyRoute = $destroyRoute;
        $this->routeAfterDelete = $routeAfterDelete;
        $this->confirmationText = $confirmationText;
        $this->successText = $successText;
        $this->errorText = $errorText;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.alerts.delete-data-confirmation');
    }
}
