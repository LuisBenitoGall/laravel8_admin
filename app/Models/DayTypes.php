<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DayTypes extends Model{
    /**
     * 1. Obtener empresa.
     * 2. Configuración por tipo de fecha y año.
     * 3. Número de tipo de días por usuario y año.
     * 4. Calculando jornadas acumuladas.
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $table = 'day_types';

    protected $fillable = [
        'type', 'days', 'company_id'
    ];

    protected $dates = ['deleted_at'];

    /**
     * 1. Obtener empresa.
     */
    public function company(){
        return $this->belongsTo(Company::class);
    }   

    /**
     * 2. Configuración por tipo de fecha y año.
     */
    public function dayConfig($day_type_id, $year){
        $data = DayTypesConfig::select('min', 'max', 'max_per_month')
        ->where('day_type_id', $day_type_id)
        ->where('year', $year)
        ->first();

        return $data;
    }

    /**
     * 3. Número de tipo de días por usuario y año.
     */
    public function getNumDayTypeByUser($user_id, $day_type_id, $year){
        $data = WorkUserCalendar::select('id')
        ->where('user_id', $user_id)
        ->where('day_type_id', $day_type_id)
        ->whereYear('date', $year)
        ->get();

        return $data;
    }

    /**
     * 4. Calculando jornadas acumuladas.
     */
    public function getCumulativeDays($user_id, $any){
        if($this->cumulative){
            $prevYear = $any - 1;

            //Días a realizar:
            $prevYearDays = $this->dayConfig($this->id, $prevYear);

            //Días realizados:
            $prevUserDays = $this->getNumDayTypeByUser($user_id, $this->id, $prevYear);

            $acumulados = $prevYearDays && $prevYearDays['max']? $prevYearDays['max'] - $prevUserDays->count():0; 

            //Buscamos jornadas remanentes:
            $residuary_days = ResiduaryDays::select('days')
            ->where('user_id', $user_id)
            ->where('day_type_id', $this->id)
            ->where('year', $prevYear)
            ->first();

            if($residuary_days && $residuary_days->days) $acumulados = $acumulados + $residuary_days->days;

            return $acumulados;  

        }else{
            return 0;
        }
    }

    /**
     * 5. Obtener número de jornadas por usuario.
     */
    public function getDayTypeQtyByUser($user_id, $year){
        $data = DayTypesUsersConfig::select('qty')
        ->where('user_id', $user_id)
        ->where('day_type_id', $this->id)
        ->where('year', $year)
        ->first();

        return $data;
    }
}
