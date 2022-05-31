<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Helpers\helpers;

class dropzone extends Component{
    public $icon;
    public $icons;
    public $id;
    public $mimes_img;
    public $msg;
    public $url;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $icon = 'camera-retro',
        $icons = [],
        $id = false,
        $mimes_img = [],
        $msg = 'arrastra_fotos',
        $url
    ){
        $this->icon = $icon;
        $this->icons = json_encode(helpers::fileIcons());
        $this->id = $id;
        $this->mimes_img = json_encode(helpers::imgMimeTypes());
        $this->msg = $msg;
        $this->url = $url;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render(){
        return view('components.dropzone');
    }
}
