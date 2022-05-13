<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkCalendar extends Model{
    use HasFactory;

    protected $table = 'work_calendars';

    protected $fillable = [
        'workplace_id', 'day_type_id', 'date', 'hours'
    ];
}
