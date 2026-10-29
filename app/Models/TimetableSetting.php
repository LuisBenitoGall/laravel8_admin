<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimetableSetting extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'timetable_settings';

    protected $fillable = ['company_id'];
}
