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
    public $select2;
    /**
     * Create a new component instance.
     */
    public function __construct($label, $name, $id=null,$required = true, $multiple = false, $modal = false, $select2 = true)
    {
        $this->label = $label;
        $this->name = $name;
        $this->id = $id ?? $name;
        $this->required = $required;
        $this->multiple = $multiple;
        $this->modal = $modal;
        $this->select2 = $select2;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.select');
    }
}
