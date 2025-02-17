<?php

namespace App\View\Components\Charts;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AttendanceDoughnutChart extends Component
{
    public $chartId;
    public $datas;
    /**
     * Create a new component instance.
     */
    public function __construct($chartId, $datas)
    {
        $this->chartId = $chartId;
        $this->datas = $datas;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.charts.attendance-doughnut-chart');
    }
}
