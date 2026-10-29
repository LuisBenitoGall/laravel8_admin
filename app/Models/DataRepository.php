<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataRepository extends Model{
    use HasFactory;

    protected $table = 'data_repositories';
}
