<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Helpers\helpers;

class lightboxGalleryDocs extends Component{
    public $docs;
    //public $image_mimes;
    public $other_files;
    public $schema;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $docs = false,
        $other_files = [],
        $schema = false
    ){
        $this->docs = $docs;
        //$this->image_mimes = helpers::imgMimeTypes();
        $this->other_files = $other_files;
        $this->schema = $schema;
    }

    /**
     * Render image + controls.
     */
    public function renderImg($doc, $gallery = false){
        $render = '<div class="gallery-item col-sm-3 mb-4">
            <div class="gallery-img">';
            if($gallery){
                $render .= '<a href="/img/'.$this->schema.'/'.$doc->doc.'" data-gallery="">
                    <img src="/img/'.$this->schema.'/'.$doc->doc.'">
                    </a>';
            }else{
                $render .= '<span class="icon">
                    <i class="fa fa-file mb-2"></i><br>
                    '.$doc->doc.'
                </span>';    
            }

            $render .= '</div><div class="gallery-controls">';
                //Descargar:
                if(!$gallery){
                    $render .= '<span>
                        <a href="/img/'.$this->schema.'/'.$doc->doc.'" download="'.$doc->doc.'" rel="tooltip" title="'.trans('textos.archivo_descargar').'">
                            <i class="fa fa-download"></i>
                        </a>
                    </span>';
                }

                //Eliminar:
                $render .= '<span>
                    <a href="/admin/'.$this->schema.'-docs/'.$doc->id.'/destroy" class="btn-confirm btn-controls" data-msg="'.ucfirst(trans('textos.archivo_confirm_eliminar')).'" rel="tooltip" title="'.ucfirst(trans('textos.archivo_eliminar')).'">
                        <i class="fa fa-trash"></i>
                    </a>
                </span> 
            </div>
        </div>';   

        return $render; 
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render(){
        return view('components.lightbox-gallery-docs');
    }
}
