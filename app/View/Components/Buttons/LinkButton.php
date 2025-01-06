<?php

namespace App\View\Components\Buttons;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LinkButton extends Component
{
    public $color;
    public $text;
    public $id;
    public $size;
    public $margin;
    public $icon;
    public $href;
    /**
     * Create a new component instance.
     */
    public function __construct($color = 'primary', $text = '', $id = null, $size = '', $margin = '', $icon = '', $href = '')
    {
        $this->color = $color;
        $this->text = $text;
        $this->id = $id;
        $this->size = $size;
        $this->margin = $margin;
        $this->icon = $icon;
        $this->href = $href;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.buttons.link-button');
    }
}
