<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTimetables extends Model{
    /**
     * 1. Actualizar cierre de horarios personalizados.
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $table = 'user_timetables';

    protected $dates = ['deleted_at']; 

    /**
     * 1. Actualizar cierre de horarios personalizados.
     */
    static public function updateToTimetables($employee_id = false, $to){
        if($employee_id){
            UserTimetables::where('employee_id', $employee_id)
            ->where(function($query) use($to){
                $query->whereDate('to', '>', $to)
                ->orWhereNull('to');
            })          
            ->update(['to' => $to]);    
        }
    }
}
