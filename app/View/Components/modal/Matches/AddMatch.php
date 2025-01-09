<?php

namespace App\View\Components\modal\Matches;

use App\Models\Competition;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AddMatch extends Component
{
    public Competition $competition;
    /**
     * Create a new component instance.
     */
    public function __construct(Competition $competition)
    {
        $this->competition = $competition;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.matches.add-match');
    }
}
