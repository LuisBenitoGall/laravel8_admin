<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDoc extends Model{
    use HasFactory;
    
    protected $table = 'user_docs';

    /**
     * Relación con user:
     */
    public function user(){
        return $this->belongsTo(User::class);
    }   
}
