<?php

namespace App\View\Components;

use Illuminate\View\Component;

class selectStatus extends Component{
    public $options;
    public $id;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $options,
        $id = false
    ){
        $this->options = $options;
        $this->id = $id;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render(){
        return view('components.select-status');
    }
}
