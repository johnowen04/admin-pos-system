<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ActionButtons extends Component
{
    public $cancelRoute;
    public $submitRoute;

    /**
     * Create a new component instance.
     *
     * @param string $cancelRoute
     * @param string $submitRoute
     */
    public function __construct($cancelRoute, $submitRoute)
    {
        $this->cancelRoute = $cancelRoute;
        $this->submitRoute = $submitRoute;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.action-buttons');
    }
}