<?php

namespace App\View\Components;

use Illuminate\View\Component;

class input extends Component{
    public $autocomplete;
    public $autofocus;
    public $class;
    public $datas;
    public $disabled;
    public $group;
    public $groupvalue;
    public $id;
    public $info;
    public $lw;
    public $max;
    public $maxlength;
    public $min;
    public $name;
    public $placeholder;
    public $readonly;
    public $required;
    public $type;
    public $value;
    public $warning;
    public $wire;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $autocomplete = false,
        $autofocus = false,
        $class = '',
        $datas = false,
        $disabled = false,
        $group = false,
        $groupvalue = '',
        $id = false,
        $info = false,
        $lw = false,
        $max = false,
        $maxlength = false,
        $min = false,
        $name,
        $placeholder = false,
        $readonly = false,
        $required = false,
        $type,
        $value = '',
        $warning = false,
        $wire = false
    ){
        $this->type = $type;
        $this->name = $name;
        $this->id = $id;
        $this->class = $class;
        $this->disabled = $disabled;
        $this->value = $value;
        $this->maxlength = $maxlength;
        $this->min = $min;
        $this->max = $max;
        $this->required = $required;
        $this->readonly = $readonly;
        $this->placeholder = $placeholder;
        $this->autocomplete = $autocomplete;
        $this->autofocus = $autofocus;
        $this->warning = $warning;
        $this->info = $info;
        $this->lw = $lw;
        $this->group = $group;
        $this->groupvalue = $groupvalue;
        $this->wire = $wire;

        $datos = '';
        if($datas){
            $arr = explode('|', $datas);

            if(count($arr)){
                foreach($arr as $row){
                    $p = explode(':', $row);
                    $datos .= 'data-'.$p[0].'="'.$p[1].'" ';
                }
            }
        }
        $this->datas = $datos;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render(){
        return view('components.input');
    }
}
