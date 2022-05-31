<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Importaciones extends Model{
    use HasFactory;

    protected $table = 'importaciones';

    protected $fillable = ['texto'];
}
