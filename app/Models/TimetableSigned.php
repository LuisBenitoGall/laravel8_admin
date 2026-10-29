<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TimetableSigned extends Model{
    /**
     * 1. Obtener horarios firmados por mes y año.
     */

    use HasFactory;

    protected $table = 'timetable_signed';

    /**
     * 1. Obtener firmas pendientes por mes y año.
     */
    public static function getPendentSigns($year, $month){
        $data = TimetableSigned::where('company_id', session('currentCompany'))
        ->where('year', $year)
        ->where('month', $month)
        ->where('signed', false)
        ->count('id');

        return $data;
    }
}
