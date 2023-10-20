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
use App\Models\AccountingAccountType;

class AccountingAccountTypeController extends Controller{
    /**
     * 1. Relación de grupos contables.
     * 1.1. Lista de grupos contales para DataTable.
     * 2. Formulario nuevo grupo.
     * 3. Guardar nuevo grupo.
     * 4. Editar grupo.
     * 5. Actualizar grupo.
     * 6. Eliminar grupo.
     * 7. Mostrar listado de grupos.
     */
    
    private $option = 'configuracion';
    private $module = 'settings';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación create:
        if(isset($data['submit_save'])) {
            return Validator::make($data, [
                'code' => "required|integer|unique:accounting_account_types,code,".$data['code'].",id,deleted_at,NULL",
                'name' => 'required'
            ],[
                'code.unique' => trans('textos.grupo_contable_codigo_repetido')
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])) {
            return Validator::make($data, [
                'id' => "required|integer",
                'name' => 'required'
            ]);
        }
    }
    //unique:users,email,NULL,id,deleted_at,NULL

    /**
     * 1. Relación de bancos.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.grupos_contables'),
            'module' => $this->module,
            'slug' => 'accounting-account-types'
        ];

        return view('admin.accounting_account_types.index')->with(compact('header'));
    }  

    /**
     * 1.1. Lista de grupos contales para DataTable.
     */
    public function getAccountingAccountTypesDT(){
        $data = AccountingAccountType::orderBy('id', 'ASC');

        return DataTables::of($data)
        ->addColumn('code', function ($row){
            return $row->code;
        }) 
        ->addColumn('name', function ($row){
            return $row->name;
        }) 
        ->addColumn('level', function ($row){
            return strlen($row->code);
        }) 
        ->addColumn('acciones', function ($row){
            return view('admin.accounting_account_types.partials.dt-actions', compact('row'));
        })
        ->make(true);
    }

    /**
     * 2. Formulario nuevo grupo.
     */
    public function create(){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.grupo_contable_nuevo',
            'module' => $this->module,
            'slug' => 'accounting-account-types'
        ]; 

        $languages = $this->languages;

        return view('admin.accounting_account_types.create')->with(compact('header', 'languages'));    
    }

    /**
     * 3. Guardar nuevo grupo.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return redirect('/admin/accounting-account-types/create')
                ->withErrors($validator)
                ->withInput();
        }

        $group = new AccountingAccountType();
        $group->autoreference = '0';
        $group->code = $request->code;
        $group->name = $request->name;
        $group->save();

        $msg = trans('textos.grupo_creado_ok');
        return redirect()->route('accounting-account-types.edit', $group)->with(compact('msg'));
    }

    /**
     * 4. Editar grupo.
     */
    // public function edit(AccountingAccountType $group){
    //     $header = [
    //         'option' => $this->option,
    //         'suboption' => 'textos.grupo_contable_editar',
    //         'module' => $this->module,
    //         'slug' => 'accounting-account-types'
    //     ]; 

    //     return view('admin.accounting_account_types.edit')->with(compact('header', 'group'));     
    // }

    /**
     * 5. Actualizar grupo.
     */
    public function update(Request $request){
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return redirect('/admin/accounting-account-types/'.$data->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $type = AccountingAccountType::find($request->id);
        $type->name = $request->name;
        $type->save();

        $msg = trans('textos.grupo_actualizado');
        return redirect()->route('accounting-account-types.index')->with(compact('msg'));
    }

    /**
     * 6. Eliminar grupo.
     */
    public function destroy(AccountingAccountType $group){
        $group->delete();
        
        $msg = trans('textos.grupo_deleted');
        return redirect()->route('accounting-account-types.index')->with(compact('msg'));    
    }

    /**
     * 7. Mostrar listado de grupos.
     */
    public function show(){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.grupos_contables',
            'module' => $this->module,
            'slug' => 'accounting-account-types'
        ]; 

        $accounts = AccountingAccountType::orderBy('id', 'ASC')->get();
    
        return view('admin.accounting_account_types.show')->with(compact('header', 'accounts'));   
    }
}
