<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sat extends Model{
    /**
     * SAT: Servicio de Asistencia Técnica
     */

    use HasFactory;
    use SoftDeletes;

    protected $table = 'sats';
}
