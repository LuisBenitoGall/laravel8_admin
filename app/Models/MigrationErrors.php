<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MigrationErrors extends Model{
    use HasFactory;

    protected $table = 'migration_errors';
}
