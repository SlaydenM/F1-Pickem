<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ChooseSession extends Component
{
    public $selectedSessionKey;
    public $sessionList;
    public $year;

    /**
     * Create a new component instance.
     */
    public function __construct($selectedSessionKey = null, $sessionList = [], $year = null)
    {
        $this->selectedSessionKey = $selectedSessionKey;
        $this->sessionList = $sessionList;
        $this->year = $year;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.choose-session');
    }
}
