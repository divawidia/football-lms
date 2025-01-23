<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RangeSlider extends Component
{
    public string $name;
    public bool $required;
    public string $label;
    public bool $modal;
    /**
     * Create a new component instance.
     */
    public function __construct($name, $required = true, $label = null, $modal = false)
    {
        $this->name = $name;
        $this->required = $required;
        $this->label = $label;
        $this->modal = $modal;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.range-slider');
    }
}
