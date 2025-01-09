<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ImageInput extends Component
{
    public $label;
    public $required;
    public $name;
    public $modal;
    public $value;
    /**
     * Create a new component instance.
     */
    public function __construct($name, $label = null, $required = false, $modal = false, $value = null)
    {
        $this->name = $name;
        $this->label = $label;
        $this->required = $required;
        $this->modal = $modal;
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.image-input');
    }
}
