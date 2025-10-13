<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Filepond extends Component
{
    /**
     * The input name.
     *
     * @var string
     */
    public $name;

    /**
     * Whether the field is required.
     *
     * @var bool
     */
    public $required;

    /**
     * Create a new component instance.
     *
     * @param  string  $name
     * @param  bool  $required
     * @return void
     */
    public function __construct($name, $required = false)
    {
        $this->name = $name;
        $this->required = $required;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.filepond');
    }
}
