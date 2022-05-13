<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IpAccess extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'ip_access';

    protected $dates = ['deleted_at'];
}
