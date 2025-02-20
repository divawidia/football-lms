<?php

namespace App\View\Components\modal\Subscriptions;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EditSubscriptionTaxModal extends Component
{
    public $taxes;
    /**
     * Create a new component instance.
     */
    public function __construct($taxes)
    {
        $this->taxes = $taxes;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.subscriptions.edit-subscription-tax-modal');
    }
}
