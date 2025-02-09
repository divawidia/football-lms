<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ImageInputWithPreview extends Component
{
    public string $label;
    public bool $required;
    public string $name;
    public mixed $id;
    public bool $modal;
    public mixed $value;
    /**
     * Create a new component instance.
     */
    public function __construct($label, $name, $required = true, $id = null, $modal = false, $value = null)
    {
        $this->name = $name;
        $this->label = $label;
        $this->required = $required;
        $this->id = $id ?? $name;
        $this->modal = $modal;
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.image-input-with-preview');
    }
}
