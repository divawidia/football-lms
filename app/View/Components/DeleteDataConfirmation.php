<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DeleteSubscriptionComfirmation extends Component
{
    public $deleteBtnClass;
    public $destroyRoute;
    public $routeAfterDelete;
    /**
     * Create a new component instance.
     */
    public function __construct($deleteBtnClass, $destroyRoute, $routeAfterDelete)
    {
        $this->deleteBtnClass = $deleteBtnClass;
        $this->destroyRoute = $destroyRoute;
        $this->routeAfterDelete = $routeAfterDelete;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.alerts.delete-data-confirmation');
    }
}
