<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

//Models:
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\Seat;
use App\Models\SeatModel;
use App\Models\SeatModelLine;

class SeatModelController extends Controller{
    /**
     * 1. Relación de modelos.
     * 1.1. Listado de modelos para DataTable.
     * 2. Formulario nuevo modelo.
     * 3. Guardar nuevo modelo.
     * 4. Editar modelo.
     * 5. Actualizar modelo.
     * 6. Eliminar modelo.
     */
    
    private $option = 'contabilidad';
    private $module = 'accounting';
    private $languages;
    private $currency;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        //Moneda:
        $this->currency = CompanySettings::co_currency(0);
    }   

    //0. Validator:
    protected function validator(array $data){
        //Validación create:
        if(isset($data['submit_save'])) {
            return Validator::make($data, [
                'name' => 'required'
            ]);

        //Validación update:
        } elseif (isset($data['submit_update'])) {
            return Validator::make($data, [
                'name' => 'required'
            ]);
        }
    } 

    /**
     * 1. Relación de modelos.
     */
    public function index(){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.modelos_contables',
            'module' => $this->module,
            'slug' => 'seats'
        ];  

        $company = Company::find(session('currentCompany'));

        $languages = $this->languages;

        return view('admin.seat_models.index')->with(compact('header', 'company', 'languages'));
    }

    /**
     * 1.1. Listado de modelos para DataTable.
     */
    public function getSeatModelsDT(){
        $data = SeatModels::where('company_id', session('currentCompany'));

        $diaries = helpers::accountingDiaries();

        return DataTables::of($data)
        ->addColumn('diary', function ($row) use($diaries){
            if(isset($diaries[$row->diary])) return $diaries[$row->diary];
        })
        ->addColumn('acciones', function ($row){
            return view('admin.seat_models.partials.dt-actions', compact('row'));
        })
        ->make(true);
    }

    /**
     * 2. Formulario nuevo modelo.
     */
    public function create(){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.modelo_asiento_contable_nuevo',
            'module' => $this->module,
            'slug' => 'seat-models'
        ]; 

        $diaries = helpers::accountingDiaries();

        $languages = $this->languages;

        $currency = $this->currency;

        return view('admin.seat_models.create')->with(compact('header', 'diaries', 'languages', 'currency'));        
    }

    /**
     * 3. Guardar nuevo modelo.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return redirect('/admin/seat-models/create')
                ->withErrors($validator)
                ->withInput();
        }

        $sm = new SeatModel();
        $sm->company_id = session('currentCompany');
        $sm->name = $request->name;
        $sm->diary = $request->diary;
        $sm->description = $request->description;
        $sm->created_by = Auth::user()->id;
        $sm->save();

        //Validando que no se repitan cuentas contables:
        $repeat = helpers::getRepeatValues($request->accounting_account_id);

        //Líneas:
        $model_lines = [];
        if($request->accounting_account_id){
            $model_lines['accounting_account_id'] = $request->accounting_account_id;
            $model_lines['type'] = $request->type;
            $model_lines['debit'] = $request->debit;
            $model_lines['credit'] = $request->credit;
        }

        //Numeración líneas:
        $k = 1;
        for($i = 0; $i < count($model_lines['accounting_account_id']); $i++){
            $data_line = [
                'model_id' => $sm->id,
                'line_num' => $k,
                'accounting_account_id' => $model_lines['accounting_account_id'][$i],
                'accounting_account' => NULL,
                'debit' => $model_lines['debit'][$i],
                'credit' => $model_lines['credit'][$i],
                'code_auto' => NULL,
                'type' => $model_lines['type'][$i],
                'currency_id' => $request->currency_id
            ];
            SeatModelLine::saveLine($data_line);

            $k++;
        }

        $msg = trans('textos.modelo_creado');
        return redirect()->route('seat-models.edit', $sm)->with(compact('msg'));
    }
}
