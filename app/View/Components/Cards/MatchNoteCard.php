<?php

namespace App\View\Components\Cards;

use App\Models\MatchModel;
use App\Models\MatchNote;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MatchNoteCard extends Component
{
    public MatchNote $note;
    public MatchModel $match;

    /**
     * Create a new component instance.
     */
    public function __construct(MatchNote $note, MatchModel $match)
    {
        $this->note = $note;
        $this->match = $match;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cards.match-note-card');
    }
}
