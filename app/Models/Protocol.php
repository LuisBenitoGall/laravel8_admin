<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Protocol extends Model{
    /**
     * 1. Autor.
     */

    use HasFactory;
    use SoftDeletes; 

    protected $dates = ['deleted_at']; 

    protected $table = 'protocols';
    
    protected $fillable = [
        'company_id', 'name', 'slug', 'description', 'created_by', 'status'
    ];   

    /**
     * 1. Autor.
     */
    public function author(){
        return $this->belongsTo(User::class, 'created_by');
    }
}
