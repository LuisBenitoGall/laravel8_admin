<?php

namespace App\View\Components;

use Illuminate\View\Component;

class checkbox extends Component{
    public $checked;
    public $class;
    public $datas;
    public $disabled;
    public $id;
    public $inputclass;
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
        $datas = false,
        $disabled = false,
        $id = false, 
        $inputclass = false,
        $name = '',
        $required = false,
        $style = false,
        $title = false,
        $value = '' 
    ){
        $this->name = $name;
        $this->id = $id;
        $this->class = $class;
        $this->inputclass = $inputclass;
        $this->disabled = $disabled;   
        $this->value = $value;
        $this->checked = $checked;
        $this->title = $title; 
        $this->style = $style;   
        $this->required = $required;
        $this->datas = $datas;

        if($this->datas){
            $data = $this->datas;
            $this->datas = '';
            $arr_data = explode('|', $data);

            foreach($arr_data as $r){
                $p = explode('.', $r);
                $this->datas .= 'data-'.$p[0].'="'.$p[1].'" ';
            }
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render(){
        return view('components.checkbox');
    }
}
