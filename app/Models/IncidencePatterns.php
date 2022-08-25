<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncidencePatterns extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'incidence_patterns';

    protected $dates = ['deleted_at'];
}
