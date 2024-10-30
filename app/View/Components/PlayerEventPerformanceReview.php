<?php

namespace App\View\Components;

use App\Models\PlayerPerformanceReview;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PlayerEventPerformanceReview extends Component
{
    public PlayerPerformanceReview $review;
    /**
     * Create a new component instance.
     */
    public function __construct(PlayerPerformanceReview $review)
    {
        $this->review = $review;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cards.player-event-performance-review');
    }
}
