<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SidebarLogo extends Component
{
    public $logo;
    public $backgroundColor;

    public function __construct($logo = 'assets/img/kaiadmin/logo_light.svg', $backgroundColor = 'dark')
    {
        $this->logo = $logo;
        $this->backgroundColor = $backgroundColor;
    }

    public function render()
    {
        return view('components.sidebar-logo');
    }
}
