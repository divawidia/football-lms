<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ModalFormUpdateProcessing extends Component
{
    public $formId;
    public $updateDataId;
    public $routeUpdate;
    public $modalId;
    public $routeAfterProcess;

    /**
     * Create a new component instance.
     */
    public function __construct($formId, $updateDataId, $routeUpdate, $modalId, $routeAfterProcess)
    {
        $this->formId = $formId;
        $this->updateDataId = $updateDataId;
        $this->routeUpdate = $routeUpdate;
        $this->modalId = $modalId;
        $this->routeAfterProcess = $routeAfterProcess;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.alerts.modal-form-update-processing');
    }
}
