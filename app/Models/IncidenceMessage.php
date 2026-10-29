<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncidenceMessage extends Model{
    /**
     * 1. Documentos del mensaje.
     * 2. Autor del mensaje.
     * 3. Respuestas a mensajes.
     */

    use HasFactory;
    use SoftDeletes;

    protected $table = 'incidence_messages';

    protected $dates = ['deleted_at'];

    /**
     * 1. Documentos del mensaje.
     */
    public function docs() {
        return $this->hasMany(IncidenceDoc::class, 'incidence_message_id');
    }

    /**
     * 2. Autor del mensaje.
     */
    public function author(){
        return $this->belongsTo(User::class, 'user_id'); 
    }

    /**
     * 3. Respuestas a mensajes.
     */
    public function children(){
        return $this->hasMany(IncidenceMessage::class, 'response_to');
    }
}
