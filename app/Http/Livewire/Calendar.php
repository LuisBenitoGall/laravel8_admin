<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Helpers\helpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\DayTypes;
use App\Models\WorkCalendar;
use App\Models\WorkUserCalendar;

class Calendar extends Component{
    /**
     * 1. Render componente.
     * 2. Seleccionar fecha.
     * 3. Seleccionar tipo de jornada.
     */

    public $date;
    public $day_type;
    public $dayTypes;
    public $dayTypesArr = [];
    public $selectedDates = [];
    public $user = false;
    public $userDates = [];
    public $weekDays;
    public $workplace;
    public $year;

    protected $listeners = ['selectDayType'];

    public function mount($year, $workplace, $user){
        $this->day_type = false;
        $this->dayTypes = DayTypes::where('company_id', session('currentCompany'))->get();
        //Array de colores de los tipos de jornada:
        $arrDayTypesColor = [];
        foreach($this->dayTypes as $dt){
            $arrDayTypesColor[$dt->id] = $dt->color;
            $this->dayTypesArr[$dt->id] = $dt->name;
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
                'color' => $dd->day_type_id && isset($arrDayTypesColor[$dd->day_type_id])? $arrDayTypesColor[$dd->day_type_id]:'',
                'hours' => $dd->hours
            ];
        }

        //Información del usuario:
        if($user){
            $this->user = $user;
            //Fechas del usuario:
            $user_dates = WorkUserCalendar::select('id', 'day_type_id', 'date', 'notified', 'doc', 'status')
            ->where('user_id', $user->id)
            ->whereYear('date', '=', $year)
            ->orderBy('date', 'ASC')
            ->get();

            //Componemos el array de fecha seleccionadas:
            foreach($user_dates as $ud){
                $this->userDates[$ud->date] = [
                    'id' => $ud->id,
                    'day_type' => $ud->day_type_id,
                    'color' => $ud->day_type_id && array_key_exists($ud->day_type_id, $arrDayTypesColor)? $arrDayTypesColor[$ud->day_type_id]:'',
                    'notified' => $ud->notified,
                    'doc' => $ud->doc,
                    'status' => $ud->status
                ];
            }
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

            //Acción para usuario:
            if($this->user){
                //Comprobamos si la fecha existe:
                $thisDate = WorkUserCalendar::where('user_id', $this->user->id)
                ->where('date', $fecha)
                ->first();

                //Guardar en array de fechas seleccionadas:
                $save_in_array_user = true;

                //Creamos ...:
                if(!$thisDate){
                    $thisDate = new WorkUserCalendar();
                    $thisDate->user_id = $this->user->id;
                    $thisDate->day_type_id = $this->day_type->id;
                    $thisDate->date = $this->date;
                    $thisDate->save();

                // ... o actualizamos:
                }else{
                    //Si el tipo de jornada está declarado:
                    if($thisDate->day_type_id){
                        //Si estaba validada se elimina:
                        if($thisDate->status){
                            $save_in_array_user = false;

                            //Eliminamos fecha del array de fechas del usuario:
                            unset($this->userDates[$thisDate->date]);

                            $thisDate->delete();
                            $thisDate = false;

                        //Si no está validado, se valida si el usuario dispone de los permisos:
                        }else{
                            if(Auth::user()->can('work-user-calendar.edit')){
                                $thisDate->status = true;
                                $thisDate->save();
                            }   
                        }

                    //Sin tipo de jornada declarada:
                    }else{
                        $thisDate->day_type_id = $this->day_type->id;
                        $thisDate->save();
                    }
                }

                //Guardamos en el array de fechas del usuario...
                if($save_in_array_user){
                    $this->userDates[$thisDate->date] = [
                        'id' => $thisDate->id,
                        'day_type' => $this->day_type->id,
                        'color' => $this->day_type->color,
                        'notified' => NULL,
                        'doc' => NULL,
                        'status' => $thisDate->status
                    ];
                }

            //Acción para empresa:
            }else{
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
