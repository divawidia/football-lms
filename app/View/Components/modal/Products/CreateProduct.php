<?php

namespace App\View\Components\modal\Products;

use App\Models\ProductCategory;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CreateProduct extends Component
{
    public mixed $categories;
    /**
     * Create a new component instance.
     */
    public function __construct($categories)
    {
        $this->categories = $categories;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.products.create-product');
    }
}
