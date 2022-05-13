<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'teams';

    protected $dates = ['deleted_at']; //Columna de borrado lógico

    protected $fillable = ['company_id', 'name', 'slug', 'logo', 'status'];
}
