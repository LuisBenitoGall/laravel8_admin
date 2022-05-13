<?php

namespace App\View\Components;

use Illuminate\View\Component;

class inputFileXls extends Component{
    public $name;
    public $accepted_files;
    
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $name,
        $accepted_files = '
        application/vnd.ms-excel, 
        application/vnd.ms-excel, 
        application/vnd.ms-excel, 
        application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,
        application/vnd.oasis.opendocument.spreadsheet'
    ){
        $this->name = $name;
        $this->accepted_files = $accepted_files;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render(){
        return view('components.input-file-xls');
    }
}
