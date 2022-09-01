<?php

namespace App\View\Components;

use Illuminate\View\Component;

class inputTextarea extends Component{
    public $class;
    public $disabled;
    public $id;
    public $name; 
    public $readonly;
    public $required;
    public $value;
    public $wire;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $class = '', 
        $disabled = false, 
        $id = false,
        $name,
        $readonly = false,
        $required = false,
        $value = '',
        $wire = false
    ){
        $this->class = $class;
        $this->disabled = $disabled;  
        $this->id = $id;
        $this->name = $name; 
        $this->readonly = $readonly;
        $this->required = $required;
        $this->value = $value;
        $this->wire = $wire;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render(){
        return view('components.input-textarea');
    }
}
