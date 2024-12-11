<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incidence extends Model{
    /**
     * 1. Documentos de la incidencia.
     * 2. Autor de la incidencia.
     * 3. Cliente de la incidencia.
     * 4. Cliente usuario.
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

    /**
     * 2. Autor de la incidencia.
     */
    public function author(){
        return $this->belongsTo(User::class, 'created_by'); 
    }

    /**
     * 3. Cliente de la incidencia.
     */
    public function customer(){
        return $this->belongsTo(Company::class, 'customer_id');
    }

    /**
     * 4. Cliente usuario.
     */
    public function user(){
        return $this->belongsTo(User::class, 'subject_id');   
    }
}
