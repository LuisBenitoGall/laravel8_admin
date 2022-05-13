<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class DayTypesConfig extends Model{
    use HasFactory;
    use SoftDeletes; 
    
    protected $table = 'day_types_configs';    

    protected $dates = ['deleted_at'];
}
