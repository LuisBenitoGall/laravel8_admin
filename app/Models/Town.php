<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Town extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Searchable;

    protected $table = 'towns';

    protected $fillable = ['name', 'slug', 'province_id', 'status'];
}
