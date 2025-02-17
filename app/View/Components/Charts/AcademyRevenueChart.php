<?php

namespace App\View\Components\Charts;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AcademyRevenueChart extends Component
{
    public $revenueGrowth;
    /**
     * Create a new component instance.
     */
    public function __construct($revenueGrowth)
    {
        $this->revenueGrowth = $revenueGrowth;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.charts.academy-revenue-chart');
    }
}
