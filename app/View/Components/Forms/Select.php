<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select extends Component
{
    public $label;
    public $name;
    public $id;
    public $required;
    public $multiple;
    public $modal;
    /**
     * Create a new component instance.
     */
    public function __construct($label, $name, $id=null,$required = true, $multiple = false, $modal = false)
    {
        $this->label = $label;
        $this->name = $name;
        $this->id = $id ?? $name;
        $this->required = $required;
        $this->multiple = $multiple;
        $this->modal = $modal;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.select');
    }
}
