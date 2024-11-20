<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StatsWithIconCard extends Component
{
    public $datas;
    public $title;
    public $dataThisMonth;
    public $icon;
    public $subtitle;
    /**
     * Create a new component instance.
     */
    public function __construct($datas, $title, $icon, $subtitle = null, $dataThisMonth = null)
    {
        $this->datas = $datas;
        $this->title = $title;
        $this->dataThisMonth = $dataThisMonth;
        $this->icon = $icon;
        $this->subtitle = $subtitle;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cards.stats-with-icon-card');
    }
}
