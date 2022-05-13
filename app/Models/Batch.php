<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Batch extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'batches';

    protected $dates = ['deleted_at'];
}
