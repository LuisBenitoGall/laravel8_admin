<?php

namespace App\View\Components;

use Illuminate\View\Component;

class datePicker extends Component{
    public $class;
    public $disabled;
    public $format;
    public $id;
    public $info;
    public $name;
    public $readonly;
    public $required;
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
        $format,
        $id = false, 
        $info = false,
        $name, 
        $readonly = false,
        $required = false,
        $value = '',
        $warning = false
    ){
        $this->class = $class;
        $this->disabled = $disabled;
        $this->id = $id;
        $this->name = $name;
        $this->readonly = $readonly;
        $this->required = $required;           
        $this->value = $value;
        $this->warning = $warning; 
        $this->info = $info;

        //Formato de fecha:
        $format = str_replace('d', 'dd', $format);
        //$format = str_replace('Y', 'yyyy', $format);
        $format = str_replace('m', 'mm', $format);
        $this->format = $format;  
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render(){
        return view('components.date-picker');
    }
}
