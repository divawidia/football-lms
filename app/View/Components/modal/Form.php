<?php

namespace App\View\Components\modal;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Form extends Component
{
    public $id;
    public $title;
    public $formId;
    public $size;
    public $editForm;

    public function __construct($id, $title = '', $formId = '', $size = '', $editForm = false)
    {
        $this->id = $id;
        $this->title = $title;
        $this->formId = $formId;
        $this->size = $size;
        $this->editForm = $editForm;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.form');
    }
}
