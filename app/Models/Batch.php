<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Batch extends Model{
    /**
     * 1. Crear nuevo lote.
     */

    use HasFactory;
    use SoftDeletes;

    protected $table = 'batches';

    protected $dates = ['deleted_at'];

    /**
     * 1. Crear nuevo lote.
     */
    public function storeBatch($data){
        
    }
}
