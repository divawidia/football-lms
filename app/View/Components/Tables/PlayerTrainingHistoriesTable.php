<?php

namespace App\View\Components\Tables;

use App\Models\Player;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PlayerTrainingHistoriesTable extends Component
{
    public Player $player;
    /**
     * Create a new component instance.
     */
    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.tables.player-training-histories-table');
    }
}
