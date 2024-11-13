<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AddOpponentTeamMatchInCompetitionModal extends Component
{
    public $competition;
    /**
     * Create a new component instance.
     */
    public function __construct($competition)
    {
        $this->competition = $competition;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.add-opponent-team-match-in-competition-modal');
    }
}
