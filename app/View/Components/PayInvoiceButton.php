<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PayInvoiceButton extends Component
{
    public $btnClass;
    public $btnText;
    public $invoiceId;
    public $snapToken;
    /**
     * Create a new component instance.
     */
    public function __construct($btnClass, $btnText,$invoiceId, $snapToken)
    {
        $this->btnClass = $btnClass;
        $this->btnText = $btnText;
        $this->invoiceId = $invoiceId;
        $this->snapToken = $snapToken;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.buttons.pay-invoice-button');
    }
}
