<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class ProjectGroupItems extends Model{
    use HasFactory;
    use SoftDeletes; 

    protected $dates = ['deleted_at']; 
    
    protected $table = 'project_group_items';

    protected $fillable = [
        'group_id', 'project_id', 'created_by',
    ];
}
