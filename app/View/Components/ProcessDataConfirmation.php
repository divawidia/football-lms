<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProcessDataConfirmation extends Component
{
    public $btnClass;
    public $processRoute;
    public $method;
    public $routeAfterProcess;
    public $confirmationText;
    public $successText;
    public $errorText;
    /**
     * Create a new component instance.
     */
    public function __construct($btnClass, $processRoute, $method ,$routeAfterProcess, $confirmationText, $successText, $errorText)
    {
        $this->btnClass = $btnClass;
        $this->processRoute = $processRoute;
        $this->method = $method;
        $this->routeAfterProcess = $routeAfterProcess;
        $this->confirmationText = $confirmationText;
        $this->successText = $successText;
        $this->errorText = $errorText;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.alerts.process-data-confirmation');
    }
}
