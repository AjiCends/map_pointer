<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Map extends Component
{
    public $coordinates;
    public $id;
    public $groupByProgram;

    public function __construct($coordinates, $id = 'map', $groupByProgram = false)
    {
        $this->coordinates = $coordinates;
        $this->id = $id;
        $this->groupByProgram = $groupByProgram ?? false;
    }

    public function render()
    {
        return view('components.map');
    }
}
