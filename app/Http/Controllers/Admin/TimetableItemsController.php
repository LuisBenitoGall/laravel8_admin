<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

//Models:
use App\Models\DayTypes;
use App\Models\TimetableItems;

class TimetableItemsController extends Controller{
    /**
     * 1. Listado de items de la empresa.
     * 1.1. Listado de items para DataTable.
     * 2. Formulario nuevo concepto.
     * 3. Guardar nuevo concepto.
     * 4. Editar item.
     * 5. Actualizar item.
     * 6. Eliminar item.
     * 7. Exportar valores a otras empresas vinculadas.
     * 8. Guardar regla de concepto obligatorio.
     * 9. Estado de concepto horario.
     */

    private $option = 'horarios';
    private $module = 'timetables';
    private $languages;

    public function __construct(){
        $this->middleware('module_setted:timetables');
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save item:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|string|max:191',
                'type' => 'required|numeric|min:1|max:2'
            ]);

        //Validación update registro horario:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|string|max:191'
            ]);

        //Validación regla de concepto obligatorio:
         }elseif(isset($data['submit_rule'])){
            return Validator::make($data, [
                'id' => 'required|integer',
                'tabla' => 'required',
                'campo' => 'required',
                'regla' => 'required',
                'valor' => 'required'
            ]);
        }
    }

    /**
     * 1. Listado de items de la empresa.
     */
    public function index(){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.horarios_conceptos_',
            'module' => $this->module,
            'slug' => 'timetable-items'
        ];

        //Comprobamos si existen los movimientos básicos de entrada y salida, y se crean en caso de no existir:
        $basics = TimetableItems::select('id')
        ->where('company_id', session('currentCompany'))
        ->where('basic_item', 1)
        ->get();

        if(!$basics->count()){
            //Entrada:
            $entry = new TimetableItems();
            $entry->company_id = session('currentCompany');
            $entry->name = trans('textos.entrada');
            $entry->slug = Str::slug(trans('textos.entrada'));
            $entry->basic_item = true;
            $entry->type = 1;
            $entry->icon = 'sign-in-alt';
            $entry->save();

            //Salida:
            $exit = new TimetableItems();
            $exit->company_id = session('currentCompany');
            $exit->name = trans('textos.salida');
            $exit->slug = Str::slug(trans('textos.salida'));
            $exit->basic_item = true;
            $exit->type = 2;
            $exit->icon = 'sign-out-alt';
            $exit->save();
        }

        return view('admin.timetable_items.index')->with(compact('header'));
    }

    /**
     * 1.1. Listado de items para DataTable.
     */
    public function getTimetableItemsDT(){
        $data = TimetableItems::where('company_id', session('currentCompany'))
        ->orderBy('name', 'ASC');

        return DataTables::of($data)
        ->addColumn('name', function($row){
            return ucfirst($row->name); 
        })
        ->addColumn('type', function($row){
            if($row->type == 1){
                return ucfirst(trans('textos.entrada'));
            }else{
                return ucfirst(trans('textos.salida'));  
            }
        })
        ->addColumn('fixed', function($row){
            $fixed = $row->fixed? $row->fixed.'"':false;
            if($row->fixed_priority && $fixed){
                $fixed .= ' <i class="fa fa-asterisk text-warning" style="font-size: 9px;"></i>';
            }
            if($fixed){
                return $fixed;
            }else{
                return '';
            }
        })
        ->addColumn('min_hour', function($row){
            if($row->min_hour){
                return $row->min_hour.'h';
            }else{
                return '';
            }
        })
        ->addColumn('min', function($row){
            if($row->min){
                return $row->min.'"';
            }else{
                return '';
            }
        })
        ->addColumn('max', function($row){
            if($row->max){
                return $row->max.'"';
            }else{
                return '';
            }
        })
        ->addColumn('icon', function($row){
            if($row->icon){
                return '<span class="fa fa-'.$row->icon.'"></span>';
            }else{
                return '';
            }

        })
        ->addColumn('color', function($row){
            if($row->color){
                return '<span class="colorEx mr-2" style="background-color: '.$row->color.'"></span> '.$row->color;   
            }else{
                return '';
            }
        })
        ->addColumn('reviewable', function($row){
            if($row->reviewable){
                return '<i class="fa fa-check text-success"></i>';
            }else{
                return '';    
            }
        })
        ->addColumn('acciones', function($row){
            return view('admin.timetable_items.partials.dt-actions', compact('row')); 
        })
        ->rawColumns(['fixed', 'icon', 'color', 'reviewable'])
        ->make(true);
    }

    /**
     * 2. Formulario nuevo concepto.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.concepto_nuevo',
            'module' => $this->module,
            'slug' => 'timetable-items'
        ];

        return view('admin.timetable_items.create')->with(compact('header'));
    }

    /**
     * 3. Guardar nuevo concepto.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/timetable-items/create')
                ->withErrors($validator)
                ->withInput();
        }

        $slug = Str::slug($request->name);
        $reviewable = ($request->input('reviewable'))? 1:0;

        $item = new TimetableItems();
        $item->company_id = session('currentCompany');
        $item->name = $request->name;
        $item->slug = $slug;
        $item->type = $request->type;
        $item->fixed = $request->fixed;
        $item->fixed_priority = $request->fixed_priority? 1:0;
        $item->icon = $request->icon;
        $item->min = $request->min;
        $item->max = $request->max;
        $item->qty = $request->qty;
        $item->min_hour = $request->min_hour;
        $item->color = $request->color;
        $item->reviewable = $reviewable;
        $item->required = $request->required;
        $item->required_warning = $request->required_warning;
        $item->with_authorization = $request->with_authorization? 1:0;
        $item->save();

        //Indicamos si el movimiento -de entrada- tiene movimiento exclusivo de salida:
        $own_output = NULL;
        if($request->type == 1 && $request->add_output){
            $name_out = ucfirst(trans('textos.salida')).' '.$item->name;
            $slug_out = Str::slug($name_out);

            $item_out = new TimetableItems();
            $item_out->company_id = session('currentCompany');
            $item_out->name = $name_out;
            $item_out->slug = $slug_out;
            $item_out->type = 2;
            $item_out->own_output = $item->id;
            $item_out->color = $item->color;
            $item_out->save();
        }

        $msg = trans('textos.concepto_creado_ok');
        return redirect()->route('timetable-items.index')->with(compact('msg'));
    }

    /**
     * 4. Editar item.
     */
    public function edit(TimetableItems $item){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.concepto_editar',
            'module' => $this->module,
            'slug' => 'timetable-items'
        ];

        $day_types = DayTypes::select('id', 'name')
        ->where('company_id', session('currentCompany'))
        ->get();

        return view('admin.timetable_items.edit', compact('header', 'item', 'day_types'));
    }

    /**
     * 5. Actualizar item.
     */
    public function update(Request $request, TimetableItems $item){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/timetable-items/'.$item->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $slug = Str::slug($request->name);
        $reviewable = ($request->reviewable)? 1:0;

        $item->name = $request->name;
        $item->slug = $slug;
        $item->fixed = $request->fixed;
        $item->fixed_priority = $request->fixed_priority? 1:0;
        $item->icon = $request->icon;
        $item->min = $request->min;
        $item->max = $request->max;
        $item->qty = $request->qty;
        $item->min_hour = $request->min_hour;
        $item->color = $request->color;
        $item->reviewable = $reviewable;
        $item->required = $request->required;
        $item->required_warning = $request->required_warning;
        $item->with_authorization = $request->with_authorization? 1:0;
        $item->save();

        $msg = trans('textos.concepto_updated');
        return redirect()->route('timetable-items.edit', $item)->with(compact('msg'));
    }

    /**
     * 6. Eliminar item.
     */
    public function destroy(TimetableItems $item){
        $item->delete();

        $msg = trans('textos.concepto_deleted');
        return redirect()->route('timetable-items.index')->with(compact('msg'));
    }

    /**
     * 7. Exportar valores a otras empresas vinculadas.
     */
    public function export(Request $request){
        if(is_array($request->companies) && count($request->companies)){
            $data = TimetableItems::where('company_id', session('currentCompany'))
            ->orderBy('id', 'ASC
                ')
            ->get();

            if($data->count()){
                foreach($request->companies as $co){
                    //Guardamos el id del anterior registro por si es preciso para el own_output:
                    $prev_id = '';

                    foreach($data as $row){
                        //Comprobamos que no exista el registro:
                        $exist = TimetableItems::select('id')
                        ->where('company_id', $co)
                        ->where('slug', $row->slug)
                        ->first();

                        if(!$exist){
                            $item = new TimetableItems();
                            $item->company_id = $co;
                            $item->name = $row->name;
                            $item->slug = $row->slug;
                            $item->type = $row->type;
                            $item->fixed = $row->fixed;
                            $item->icon = $row->icon;
                            $item->min = $row->min;
                            $item->max = $row->max;
                            $item->qty = $row->qty;
                            $item->min_hour = $row->min_hour;
                            $item->color = $row->color;
                            $item->reviewable = $row->reviewable;
                            $item->save();
                        }

                    }
                }

                $msg = trans('textos.exportacion_ok');
                return redirect()->route('timetable-items.index')->with(compact('msg'));
            }else{
                $alert = trans('textos.exportacion_vacia');
            }

        }else{
            $alert = trans('textos.exportacion_vacia');
        }

        return redirect()->route('timetable-items.index')->with(compact('alert'));
    }

    /**
     * 8. Guardar regla de concepto obligatorio.
     */
    public function storeRule(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/timetable-items/'.$request->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $arr = [];
        $arr['tabla'] = $request->tabla;
        $arr['campo'] = $request->campo;
        $arr['regla'] = $request->regla;
        $arr['valor'] = $request->valor;
        $rule = serialize($arr);

        TimetableItems::where('id', $request->id)
        ->update(['required_condition' => $rule]);

        $msg = trans('textos.condicion_updated');
        return redirect()->route('timetable-items.edit', $request->id)->with(compact('msg'));
    }

    /**
     * 9. Estado de concepto horario.
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){
            $item = TimetableItems::find($id);
            $item->status = $status;
            $item->save();

            $resp = ($item)? true:false;
            return response()->json($resp);
        }    
    }
}
