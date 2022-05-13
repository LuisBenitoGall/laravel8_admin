<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'countries';

    protected $fillable = ['name', 'slug', 'code', 'alfa2', 'alfa3', 'flag', 'status'];
}
