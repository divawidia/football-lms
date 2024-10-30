<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class WarningAlert extends Component
{
    public $text;
    public $createRoute;
    /**
     * Create a new component instance.
     */
    public function __construct($text, $createRoute = null)
    {
        $this->text = $text;
        $this->createRoute = $createRoute;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.alerts.warning-alert');
    }
}
