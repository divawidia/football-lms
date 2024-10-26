<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PlayerSkillStatsRadarChart extends Component
{
    public $labels;
    public $datas;
    public $chartId;
    public $borderColor;
    public $backgroundColor;
    /**
     * Create a new component instance.
     */
    public function __construct($labels, $datas, $chartId = 'skillStatsChart', $borderColor = null, $backgroundColor = null)
    {
        $this->labels = $labels;
        $this->datas = $datas;
        $this->chartId = $chartId;
        $this->borderColor = $borderColor;
        $this->backgroundColor = $backgroundColor;
    }
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.chart.player-skill-stats-radar-chart');
    }
}
