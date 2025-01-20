<?php

namespace App\View\Components;

use App\Models\MatchNote;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EventNoteCard extends Component
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
        return view('components.cards.event-note');
    }
}
