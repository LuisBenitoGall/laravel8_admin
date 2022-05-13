<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupMembers extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'group_members';

    protected $fillable = ['group_id', 'user_id', 'rol', 'responsible'];    
}
