<?php

namespace App\View\Components\modal\Matches;

use App\Models\MatchModel;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EditTeamMatchStats extends Component
{
    public MatchModel $match;
    /**
     * Create a new component instance.
     */
    public function __construct($match)
    {
        $this->match = $match;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.matches.edit-team-match-stats');
    }
}
