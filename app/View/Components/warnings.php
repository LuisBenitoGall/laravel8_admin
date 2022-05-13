<?php

namespace App\View\Components;

use Illuminate\View\Component;

class warnings extends Component{
    public $alert;
    public $msg;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($alert = false, $msg = false){
        $this->alert = $alert;
        $this->msg = $msg;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render(){
        return view('components.warnings');
    }
}
