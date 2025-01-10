<?php

namespace App\View\Components\Buttons;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Dropdown extends Component
{
    public $title;
    public $icon;
    public $btnColor;
    public $iconMargin;
    /**
     * Create a new component instance.
     */
    public function __construct($title = 'Action', $icon = 'keyboard_arrow_down', $btnColor = 'outline-white', $iconMargin = 'ml-3')
    {
        $this->title = $title;
        $this->icon = $icon;
        $this->btnColor = $btnColor;
        $this->iconMargin = $iconMargin;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.buttons.dropdown');
    }
}
