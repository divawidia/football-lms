<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BasicInput extends Component
{
    public $type;
    public $name;
    public $required;
    public $label;
    public $placeholder;
    public $acceptFileType;
    public $min;
    public $max;
    public $modal;
    public $value;
    /**
     * Create a new component instance.
     */
    public function __construct($type, $name, $required = true, $label = null, $placeholder = null, $acceptFileType = null, $min = null, $max = null, $modal = false, $value = null)
    {
        $this->type = $type;
        $this->name = $name;
        $this->required = $required;
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->acceptFileType = $acceptFileType;
        $this->min = $min;
        $this->max = $max;
        $this->modal = $modal;
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.basic-input');
    }
}
