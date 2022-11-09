<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimetableItems extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'timetable_items';

    protected $dates = ['deleted_at'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

   public function timeTable(){
        return $this->hasOne(Timetable::class);
   }
}
