<?php

namespace App\View\Components;

use Illuminate\View\Component;

class inputFile extends Component{
    public $name;
    public $accepted_files;
    public $lw;     // is Livewire component.

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $name,
        $accepted_files = 'image/png, image/jpeg, image/gif, application/pdf',
        $lw = false
    ){
        $this->name = $name; 
        $this->accepted_files = $accepted_files;   
        $this->lw = $lw;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render(){
        return view('components.input-file');
    }
}
