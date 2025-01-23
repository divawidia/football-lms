<?php

namespace App\View\Components\Buttons;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Route;
use Illuminate\View\Component;

class LinkButton extends Component
{
    public string $color;
    public string $text;
    public mixed $id;
    public string $size;
    public string $margin;
    public string $icon;
    public Route|string $href;
    public bool $dropdownItem;
    /**
     * Create a new component instance.
     */
    public function __construct($color = 'primary', $text = '', $id = null, $size = '', $margin = '', $icon = '', $href = '', $dropdownItem = false)
    {
        $this->color = $color;
        $this->text = $text;
        $this->id = $id;
        $this->size = $size;
        $this->margin = $margin;
        $this->icon = $icon;
        $this->href = $href;
        $this->dropdownItem = $dropdownItem;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.buttons.link-button');
    }
}
