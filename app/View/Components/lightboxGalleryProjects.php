<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Helpers\Helpers;

class LightboxGalleryProjects extends Component{
    public $docs;
    public $image_mimes;
    public $other_files;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $docs = false,
        $other_files = []
    ){
        $this->docs = $docs;
        $this->image_mimes = Helpers::imgMimeTypes();
        $this->other_files = $other_files;
    }

    /**
     * Render image + controls.
     */
    public function renderImg($doc, $gallery = false){
        $featured = $doc->featured == 1? 'destacado':'';
        $thumbnail = $doc->thumbnail == 1? 'destacado':'';

        $render = '<div class="gallery-item col-sm-3 mb-4">
            <div class="gallery-img">';
            if($gallery){
                $render .= '<a href="/img/projects/'.$doc->doc.'" data-gallery="">
                    <img src="/img/projects/'.$doc->doc.'">
                    </a>';
            }else{
                $render .= '<span class="icon">
                    <i class="fa fa-file mb-2"></i><br>
                    '.$doc->doc.'
                </span>';    
            }

            $render .= '</div><div class="gallery-controls">';
                if($gallery){
                    //Imagen destacada:
                    $render .= '<span>
                        <button class="btn-controls btnDestacar '.$featured.'" rel="tooltip" title="'.ucfirst(trans('textos.imagen_destacada')).'" data-img="'.$doc->id.'" data-project="'.$doc->project_id.'" data-modulo="project-docs">
                            <i class="fa fa-star"></i>  
                        </button>
                    </span>'; 
                  
                    //Thumbnail:
                    $render .= '<span>
                        <button class="btn-controls btnThumb '.$thumbnail.'" rel="tooltip" title="'.ucfirst(trans('textos.imagen_thumbnail')).'" data-img="'.$doc->id.'" data-project="'.$doc->project_id.'" data-modulo="project-docs">
                            <i class="fa fa-image"></i>  
                        </button>
                    </span>';                
                }                

                //Descargar:
                if(!$gallery){
                    $render .= '<span>
                        <a href="/img/projects/'.$doc->doc.'" download="'.$doc->doc.'" rel="tooltip" title="'.trans('textos.archivo_descargar').'">
                            <i class="fa fa-download"></i>
                        </a>
                    </span>';
                }

                //Eliminar:
                $render .= '<span>
                    <a href="/admin/project-docs/'.$doc->id.'/destroy" class="btn-confirm btn-controls" data-msg="'.ucfirst(trans('textos.archivo_confirm_eliminar')).'" rel="tooltip" title="'.ucfirst(trans('textos.archivo_eliminar')).'">
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
        return view('components.lightbox-gallery-projects');
    }
}
