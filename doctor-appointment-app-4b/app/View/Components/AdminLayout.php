<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AdminLayout extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $breadcrumbs = [],
        public $title = null,
        public $actions = null,
        /** Skip Livewire + WireUI scripts (faster pages e.g. import form). */
        public bool $light = false,
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('layouts.admin');
    }
}
