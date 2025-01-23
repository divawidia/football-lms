<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Textarea extends Component
{
    public $name;
    public $required;
    public $label;
    public $placeholder;
    public $modal;
    public $value;
    public $row;
    /**
     * Create a new component instance.
     */
    public function __construct($name, $required = true, $label = null, $placeholder = null, $modal = false, $value = null, $row = 5)
    {
        $this->name = $name;
        $this->required = $required;
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->modal = $modal;
        $this->value = $value;
        $this->row = $row;
    }
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.textarea');
    }
}
