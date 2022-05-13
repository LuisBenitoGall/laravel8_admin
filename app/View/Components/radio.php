<?php

namespace App\View\Components;

use Illuminate\View\Component;

class radio extends Component{
    public $checked;
    public $class;
    public $disabled;
    public $id;
    public $name;
    public $required;    
    public $style;
    public $title;
    public $value;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $checked = false,
        $class = '', 
        $disabled = false, 
        $id = false,
        $name,
        $required = false,
        $style = false,
        $title = false,
        $value = ''
    ){
        $this->checked = $checked;
        $this->class = $class;
        $this->disabled = $disabled; 
        $this->id = $id;
        $this->name = $name;  
        $this->required = $required;
        $this->style = $style;
        $this->title = $title; 
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render(){
        return view('components.radio');
    }
}
