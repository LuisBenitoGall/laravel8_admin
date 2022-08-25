<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncidenceLines extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'incidence_lines';

    protected $dates = ['deleted_at'];
}
