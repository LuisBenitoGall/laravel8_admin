<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incidence extends Model{
    /**
     * 1. Documentos de la incidencia.
     */

    use HasFactory;
    use SoftDeletes;

    protected $table = 'incidences';

    protected $dates = ['deleted_at']; 

    /**
     * 1. Documentos de la incidencia.
     */
    public function docs(){
        return $this->hasMany(IncidenceDocs::class);
    }
}
