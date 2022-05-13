<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cheat extends Model{
    use HasFactory;

    protected $table = 'cheats';

    protected $fillable = ['user_id', 'cheat', 'model_type', 'ip'];
}
