<?php

namespace App\View\Components;

use Illuminate\View\Component;

class btnSubmit extends Component{
    public $class;
    public $classdiv;
    public $confirm_msg;
    public $datas;
    public $disabled;
    public $id;
    public $inline;
    public $label;
    public $name;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $class = '', 
        $classdiv = false,
        $confirm_msg = false,
        $datas = false,
        $disabled = false,
        $id = false,
        $inline = false,
        $label,
        $name = false   
    ){
        $this->class = $class;
        $this->classdiv = $classdiv;
        $this->confirm_msg = $confirm_msg;
        $this->disabled = $disabled;
        $this->id = $id;
        $this->inline = $inline;
        $this->label = $label;
        $this->name = $name;

        $datos = '';
        if($datas){
            $arr = explode('|', $datas);

            if(count($arr)){
                foreach($arr as $row){
                    $p = explode(':', $row);
                    $datos .= ' data-'.$p[0].'="'.$p[1].'"';
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
        return view('components.btn-submit');
    }
}
