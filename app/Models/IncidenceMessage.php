<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncidenceMessage extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'incidence_messages';

    protected $dates = ['deleted_at'];
}
