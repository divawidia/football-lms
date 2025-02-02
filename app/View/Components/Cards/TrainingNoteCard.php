<?php

namespace App\View\Components\Cards;

use App\Models\Training;
use App\Models\TrainingNote;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TrainingNoteCard extends Component
{
    public TrainingNote $note;
    public Training $training;

    /**
     * Create a new component instance.
     */
    public function __construct(TrainingNote $note, Training $training)
    {
        $this->note = $note;
        $this->training = $training;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cards.training-note-card');
    }
}
