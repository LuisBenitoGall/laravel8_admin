<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//Models:
use App\Models\Timetable;
use App\Models\TimetableItems;

class TimetableController extends Controller{
    /**
     * 1. Listado de horarios por empleados.
     * 1.1. Listado de horarios por empleados para DataTable.
     * 2. Guardar fichaje horario de usuario.
     * 3. Editar horario.
     * 4. Actualizar horario.
     * 5. Eliminar registro horario.
     */
    
    private $option = 'horarios';
    private $module = 'timetables';
    private $languages;

    public function __construct(){
        $this->middleware('module_setted:timetables');
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save intervals:
        if(isset($data['submit_filter'])){
            return Validator::make($data, [
                'workplace_id' => 'required|integer'
            ]); 

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'id' => 'required|integer',
                'item_id' => 'required|integer',
                'hora' => 'required|numeric|max:23',
                'minutos' => 'required|numeric|max:59',
                'segundos' => 'required|numeric|max:59'
            ],[
                'hora.required' => trans('textos.hora_obligatoria'),
                'minutos.required' => trans('textos.hora_obligatoria'),
                'segundos.required' => trans('textos.hora_obligatoria')
            ]);
        }
    } 

    /**
     * 1. Listado de horarios por empleados.
     */
    public function index(Request $request){

    }

    /**
     * 1.1. Listado de horarios por empleados para DataTable.
     */

    /**
     * 2. Guardar fichaje horario de usuario.
     */
    public function store(TimetableItems $item){
        if($item && is_numeric($item->id)){
            //El concepto horario debe pertenecer a la empresa en sesión:
            if($item->company_id != session('currentCompany')){
                $alert = trans('textos.no_permisos_accion');
                return redirect()->route('dashboard')->with(compact('alert'));
                exit;
            }

            $dia = date('Y-m-d');
            //Formato hora:
            $hora = date("H:i");
            $revisable = $item->reviewable? 1:0;

            //Obtenemos último registro del usuario:
            $prevRecord = Timetable::select('timetables.id', 'timetables.date', 'timetables.hour', 'timetables.item_id', 'timetables.flag', 'timetable_items.min', 'timetable_items.max', 'timetable_items.fixed')
            ->join('timetable_items', 'timetables.item_id', '=', 'timetable_items.id')
            ->where('timetables.user_id', Auth::user()->id)
            ->orderBy('timetables.date', 'DESC')
            ->orderBy('timetables.hour', 'DESC')
            ->first();

            //Evitar repetir flag:
            if($prevRecord && $prevRecord->flag == $item->type){
                return back();
                exit;
            }

            //Comprobaciones registros anteriores:
            if($prevRecord){
                //La hora actual no puede ser anterior a la última hora registrada:
                if(date('H:i:s', time()) < $prevRecord->hour){
                    return back();
                    exit;    
                }

                //Evitar duplicados:
                $duplicates = Timetable::select('id')
                ->where('date', $prevRecord->date)
                ->where('hour', $prevRecord->hour)
                ->where('user_id', Auth::user()->id)
                ->where('item_id', $prevRecord->item_id)
                ->where('flag', $prevRecord->flag)
                ->get();

                if($duplicates->count() > 1){
                    foreach($duplicates as $d){
                        if($d->id != $prevRecord->id){
                            //Borrado definitivo:
                            $d->forceDelete();
                        }
                    }
                }

                //El nuevo registro horario no puede coincidir con el anterior, por lo que se suma 1 minuto en caso de coincidencia:
                if($hora.':00' == $prevRecord->hour){
                    $add_minute = strtotime('+1 minute', strtotime($hora));
                    $hora = date('H:i', $add_minute);
                }

                //Si tiene un período mínimo y el tiempo transcurrido entre el anterior registro y el momento actual es inferior a ese mínimo, la hora de entrada es la suma de la hora: 
                if($prevRecord->min > 0 && helpers::getMinutes(helpers::getTimeBetweenHours($prevRecord->hour, $hora)) < $prevRecord->min){
                    $hora = helpers::sumaHoras($prevRecord->hour, helpers::getTimeFromMinutes($prevRecord->min));
                }
                
            }


            //dd($item, $prevRecord);



            //Guardamos registro horario:
            $hour = new Timetable();
            $hour->date = $dia;
            $hour->hour = $hora;
            $hour->user_id = Auth::user()->id;
            $hour->item_id = $item->id;
            $hour->flag = $item->type;
            $hour->revision = $revisable;
            $hour->save();

            return redirect()->route('users.timetables', Auth::user()->id);

        }else{
            return redirect('/');
        }   
    }

    /**
     * 3. Editar horario de usuario.
     */
    public function edit(Request $request, Timetable $timetable){
        if($request->ajax()){
            return response()->json($timetable);
        }
    }

    /**
     * 4. Actualizar horario.
     */
    public function update(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-edit-timetable');
            return redirect('/admin/users/'.$request->user_id.'/timetables')
                ->withErrors($validator)
                ->withInput();
        }

        $hour = $request->hora.':'.$request->minutos.':'.$request->segundos;

        $timetable = Timetable::find($request->id);
        $timetable->hour = $hour;
        $timetable->item_id = $request->item_id;
        $timetable->comments = $request->comments;
        $timetable->save();

        $msg = trans('textos.registro_horario_updated');
        return redirect()->route('users.timetables', $timetable->user_id)->with(compact('msg'));
    }

    /**
     * 5. Eliminar registro horario.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Timetable $timetable){
        $user_id = $timetable->user_id;
        $add_register = $timetable->date;

        $timetable->delete();  

        $msg = trans('textos.registro_horario_deleted');
        return redirect()->route('users.timetables', [$user_id, $add_register])->with(compact('msg'));   
    }
}
