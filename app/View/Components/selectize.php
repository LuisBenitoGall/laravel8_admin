<?php

namespace App\View\Components;

use Illuminate\View\Component;

class selectize extends Component{
    public $class;
    public $disabled;
    public $id;
    public $maxlength;
    public $name;
    public $required;
    public $style;
    public $value;
    public $warning;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $class = '', 
        $disabled = false, 
        $id = false,
        $maxlength = false,
        $name, 
        $required = false,
        $style = false,
        $value = '',
        $warning = false
    ){
        $this->class = $class;
        $this->disabled = $disabled;  
        $this->id = $id;
        $this->maxlength = $maxlength;
        $this->name = $name;
        $this->required = $required;
        $this->style = $style;  
        $this->value = $value;
        $this->warning = $warning;   
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render(){
        return view('components.selectize');
    }
}
