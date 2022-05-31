<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Helpers\helpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use File;

//Models:
use App\Models\File_;

class FilesGallery extends Component{
    /**
     * 1. Render componente.
     * 2. Editar archivo.
     * 3. Actualizar archivo.
     * 4. Eliminar archivo.
     */ 
    
    public $alt;
    public $file_id;
    public $files;
    public $icons;
    public $image;
    public $mimes_img;
    public $reload;
    public $title;

    protected $listeners = ['getReload'];

    public function mount(){
        //Icons:
        $this->icons = helpers::fileIcons();

        //Mimes images:
        $this->mimes_img = helpers::imgMimeTypes();

        $this->image = false;
        $this->reload = false;
    }

    /**
     * 1. Render componente.
     */
    public function render(){
        $this->files = File_::where('company_id', session('currentCompany'))
        ->orderBy('created_at', 'DESC')
        ->get();

        return view('livewire.files-gallery');
    }

    /**
     * 2. Editar archivo.
     */
    public function edit($file){
        $this->image = false;
        $this->alt = $file['alt'];
        $this->file_id = $file['id'];
        $this->title = $file['title'];
        
        if(in_array($file['mime'], $this->mimes_img)){
            $this->image = $file['path'].$file['file'];
        }        
    }

    /**
     * 3. Actualizar archivo.
     */
    public function update(){
        File_::where('id', $this->file_id)
        ->update(['title' => $this->title, 'alt' => $this->alt]);

        //Cerrando modal:        
        $this->emit('postUpdated');
    }

    /**
     * 4. Eliminar archivo.
     */
    public function delete(File_ $file){
        File::delete(public_path().$file->path.$file->file);
        $file->delete();
        $this->render();
    }

    public function getReload($reload){
        if($reload){
            $this->render();    
        }
    }
}
