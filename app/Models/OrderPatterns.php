<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderPatterns extends Model{
    use HasFactory;
    use SoftDeletes; 

    protected $dates = ['deleted_at']; //Columna de borrado lógico  

    protected $table = 'order_patterns';
}
