<?php

namespace App\View\Components;

use Illuminate\View\Component;

class select extends Component{
    public $class;
    public $disabled;
    public $id;
    public $info;
    public $label;
    public $multiple;
    public $name;
    public $options;
    public $readonly;
    public $required;
    public $value;
    public $values;
    public $warning;
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
        $info = false,
        $label = false,
        $multiple = false,
        $name,
        $options,
        $readonly = false,
        $required = false,
        $value = '',
        $values = [],
        $warning = false,
        $wire = false
    ){
        $this->class = $class;
        $this->disabled = $disabled;  
        $this->id = $id;
        $this->info = $info;
        $this->label = $label; 
        $this->multiple = $multiple;
        $this->name = $name;
        $this->options = $options;
        $this->readonly = $readonly;
        $this->required = $required;
        $this->value = $value;
        $this->values = $values;
        $this->warning = $warning;
        $this->wire = $wire;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render(){
        return view('components.select');
    }
}
