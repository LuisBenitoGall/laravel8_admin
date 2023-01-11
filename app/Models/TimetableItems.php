<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimetableItems extends Model{
    /**
     * 1. Items por empresa de usuario.
     */

    use HasFactory;
    use SoftDeletes;

    protected $table = 'timetable_items';

    protected $dates = ['deleted_at'];

    /**
     * 1. Items por empresa de usuario.
     */
    public static function itemsByWorkplace($workplace_id){
        return TimetableItems::select('timetable_items.*')
        ->join('workplaces', 'timetable_items.company_id', '=', 'workplaces.company_id')
        ->where('workplaces.id', $workplace_id)
        ->get();
    }

    public function company(): BelongsTo{
        return $this->belongsTo(Company::class);
    }

    public function timeTable(){
        return $this->hasOne(Timetable::class);
    }
}
