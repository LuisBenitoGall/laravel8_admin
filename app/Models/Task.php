<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\Helpers;

class Task extends Model{
    /**
     * 1. Documentos de la tarea.
     * 2. Última imagen subida.
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tasks';

    protected $dates = ['deleted_at']; //Columna de borrado lógico

    protected $fillable = ['company_id', 'name', 'slug', 'logo', 'status'];

    /**
     * 1. Documentos de la tarea.
     */
    public function docs(){
        return $this->hasMany(TaskDocs::class);
    }

    /**
     * 2. Última imagen subida.
     */
    public function lastImage(){
        $docs = $this->docs()->orderBy('created_at', 'DESC')->get();

        $mimes = Helpers::imgMimeTypes();   

        foreach($docs as $doc){
            if(file_exists('./img/tasks/'.$doc->doc)){
                $type = mime_content_type('./img/tasks/'.$doc->doc);
                if(in_array($type, $mimes)){
                    return $doc;
                    break;
                }
            }
        }

        return false;
    }
}
