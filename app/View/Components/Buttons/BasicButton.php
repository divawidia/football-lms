<?php

namespace App\View\Components\Buttons;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BasicButton extends Component
{
    public $type;
    public $modalCloseIcon;
    public $color;
    public $modalDismiss;
    public $text;
    public $id;
    public $size;
    public $margin;
    public $icon;
    public $additionalClass;
    public $dropdownItem;
    public $iconColor;
    /**
     * Create a new component instance.
     */
    public function __construct($type = 'button', $modalCloseIcon = false, $color = 'primary', $modalDismiss = false, $text = '', $id = null, $size = '', $margin = '', $icon = '', $additionalClass = '', $dropdownItem = false, $iconColor = '')
    {
        $this->type = $type;
        $this->modalCloseIcon = $modalCloseIcon;
        $this->color = $color;
        $this->modalDismiss = $modalDismiss;
        $this->text = $text;
        $this->id = $id;
        $this->size = $size;
        $this->margin = $margin;
        $this->icon = $icon;
        $this->additionalClass = $additionalClass;
        $this->dropdownItem = $dropdownItem;
        $this->iconColor = $iconColor;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.buttons.basic-button');
    }
}
