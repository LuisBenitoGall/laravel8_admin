<?php

namespace App\View\Components;

use Illuminate\View\Component;

class colorPicker extends Component{
    public $class;
    public $disabled;
    public $id;
    public $info;
    public $name;
    public $required;
    public $readonly;
    public $value;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $class = '', 
        $disabled = false,
        $id = false, 
        $info = false,  
        $name,       
        $required = false,
        $readonly = false,
        $value = ''
    ){
        $this->class = $class; 
        $this->disabled = $disabled;
        $this->id = $id;
        $this->info = $info;
        $this->name = $name;
        $this->required = $required;
        $this->readonly = $readonly;
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render(){
        return view('components.color-picker');
    }
}
