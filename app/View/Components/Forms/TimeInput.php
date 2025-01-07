<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TimeInput extends Component
{
    public $id;
    public $label;
    public $required;
    public $name;
    public $placeholder;
    public $modal;
    /**
     * Create a new component instance.
     */
    public function __construct($name, $id = null, $label = null, $required = true, $placeholder = null, $modal = false)
    {
        $this->name = $name;
        $this->id = $id ?? $name;
        $this->label = $label;
        $this->required = $required;
        $this->placeholder = $placeholder;
        $this->modal = $modal;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.time-input');
    }
}
