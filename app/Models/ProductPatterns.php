<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductPatterns extends Model{
    use HasFactory;
    use SoftDeletes; //Implementamos borrado lógico

    protected $dates = ['deleted_at']; //Columna de borrado lógico  

    protected $table = 'product_patterns';
}
