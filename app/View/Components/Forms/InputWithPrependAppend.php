<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InputWithPrependAppend extends Component
{
    public string $type;
    public string $name;
    public bool $required;
    public string $label;
    public string $placeholder;
    public mixed $min;
    public mixed $max;
    public bool $modal;
    public mixed $value;
    public bool $append;
    public mixed $icon;
    public mixed $text;
    /**
     * Create a new component instance.
     */
    public function __construct($name, $type = 'text', $required = true, $label = null, $placeholder = null, $min = null, $max = null, $modal = false, $value = null, $append = true, $icon = null, $text = null)
    {
        $this->type = $type;
        $this->name = $name;
        $this->required = $required;
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->min = $min;
        $this->max = $max;
        $this->modal = $modal;
        $this->value = $value;
        $this->append = $append;
        $this->icon = $icon;
        $this->text = $text;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.input-with-prepend-append');
    }
}
