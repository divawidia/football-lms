<?php

namespace App\View\Components\Cards;

use App\Models\Match;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MatchCard extends Component
{
    public Match $match;
    public $latestMatch;
    /**
     * Create a new component instance.
     */
    public function __construct(Match $match, $latestMatch)
    {
        $this->match = $match;
        $this->latestMatch = $latestMatch;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cards.match-card');
    }
}