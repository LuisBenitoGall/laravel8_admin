<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cash extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'cashes'; 

    protected $dates = ['deleted_at'];
}
