<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model{
    /**
     * 1. Relación con user.
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $table = 'messages';

    protected $dates = ['deleted_at']; //Columna de borrado lógico

    /**
     * 1. Relación con user.
     */
    public function user(){
        return $this->belongsTo(User::class);
    }
}
