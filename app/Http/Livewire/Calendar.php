<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Helpers\helpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\DayTypes;
use App\Models\WorkCalendar;

class Calendar extends Component{
    /**
     * 1. Render componente.
     * 2. Seleccionar fecha.
     * 3. Seleccionar tipo de jornada.
     */
    
    public $date;
    //public $day_type;
    
    public $dayTypes;
    public $selectedDates = [];
    public $weekDays;
    public $workplace;
    public $year;

    protected $listeners = ['selectDayType'];

    public function mount($year, $workplace){
        $this->day_type = false;
        $this->dayTypes = DayTypes::where('company_id', session('currentCompany'))->get();
        //Array de colores de los tipos de jornada:
        $arrDayTypesColor = [];
        foreach($this->dayTypes as $dt){
            $arrDayTypesColor[$dt->id] = $dt->color;    
        }

        $this->weekDays = helpers::weekDays();
        $this->workplace = $workplace;
        $this->year = $year;

        //Obtener las fechas ya marcadas:
        $dates_data = WorkCalendar::select('id', 'day_type_id', 'date', 'hours')
        ->where('workplace_id', $workplace->id)
        ->whereYear('date', '=', $year)
        ->orderBy('date', 'ASC')
        ->get();

        //Componemos el array de fecha seleccionadas:
        foreach($dates_data as $dd){
            $this->selectedDates[$dd->date] = [
                'day_type' => $dd->day_type_id, 
                'color' => $dd->day_type_id? $arrDayTypesColor[$dd->day_type_id]:'',
                'hours' => $dd->hours
            ];    
        }
    }

    /**
     * 1. Render componente.
     */
    public function render(){
        return view('livewire.calendar');
    }

    /**
     * 2. Seleccionar fecha.
     */
    public function selectDay($fecha){
        //Si se ha seleccionado tipo de jornada:
        if($this->day_type && $this->workplace){
            $this->date = $fecha;

            //Comprobamos si la fecha existe:
            $thisDate = WorkCalendar::where('workplace_id', $this->workplace->id)
            ->where('date', $fecha)
            ->first();

            //Guardar en array de fechas seleccionadas:
            $save_in_array = true;

            //Creamos ...:
            if(!$thisDate){
                $thisDate = WorkCalendar::create([
                    'workplace_id' => $this->workplace->id,
                    'day_type_id' => $this->day_type->id,
                    'date' => $fecha,
                    'hours' => NULL
                ]);

            // ... o actualizamos:
            }else{
                //Actualizamos ...
                if($thisDate->day_type_id != $this->day_type->id){
                    $thisDate->day_type_id = $this->day_type->id; 
                    //Si la jornada NO es laborable, se eliminan las horas de trabajo:
                    if(!$this->day_type->is_working){
                        $thisDate->hours = NULL;
                    }  
                    $thisDate->save(); 

                //... o eliminamos si el tipo de jornada es el mismo que ya existía:    
                }else{
                    $thisDate->delete();
                    $save_in_array = false;
                }                
            }

            //Guardamos en el array de fechas seleccionadas...
            if($save_in_array){
                $this->selectedDates[$thisDate->date] = [
                    'day_type' => $this->day_type->id, 
                    'color' => $this->day_type->color,
                    'hours' => ''
                ];
            //... o eliminamos del array:
            }else{
                unset($this->selectedDates[$thisDate->date]);
            }
        }
    }

    /**
     * 3. Seleccionar tipo de jornada.
     */
    public function selectDayType($day_type){
        foreach($this->dayTypes as $dt){
            if($day_type == $dt->id){
                $this->day_type = $dt;
                break;    
            }
        }
    }

    /**
     * 4. Cancelar selección de tipo de jornada.
     */
    public function cancelDayType(){
        $this->day_type = false;   
    }
}
