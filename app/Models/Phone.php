<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Phone extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'phones';

    protected $dates = ['deleted_at']; //Columna de borrado lógico

    protected $fillable = ['user_id', 'workplace_id', 'phone_number', 'concept']; 
}
