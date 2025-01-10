<?php

namespace App\View\Components\Tabs;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Item extends Component
{
    public $active;
    public $title;
    public $link;
    /**
     * Create a new component instance.
     */
    public function __construct($title, $link, $active = false)
    {
        $this->title = $title;
        $this->link = $link;
        $this->active = $active;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.tabs.item');
    }
}
