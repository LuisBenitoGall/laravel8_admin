<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

//Models:
use App\Models\Bank;

class BankController extends Controller{
    /**
     * 1. Relación de bancos.
     * 1.1. Listado de bancos para DataTable.
     * 2. Formulario nuevo banco.
     * 3. Guardar nuevo banco.
     * 4. Editar banco.
     * 5. Actualizar banco.
     * 6. Modificar status de banco.
     * 7. Eliminar banco.
     * 8. Generar .json de bancos.
     */
    
    private $option = 'configuracion';  
    private $module = 'settings';
    private $languages;

    public function __construct(){
        $this->languages = helpers::languages();
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required_without:company_id|max:255',
                'tradename' => 'nullable|max:255',
                'eu_code' => 'required'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => "required|string|max:255",
                'tradename' => 'nullable|max:255',
                'eu_code' => 'required'
            ]);
        }
    } 

    /**
     * 1. Relación de bancos.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.bancos'),
            'module' => $this->module,
            'slug' => 'banks'
        ];

        //Importación de datos desde .json. Se ejecuta 1 vez.
        Bank::import();

        return view('admin.banks.index')->with(compact('header'));
    }   

    /**
     * 1.1. Listado de bancos para DataTable.
     */
    public function getBanksDT(){
        $data = Bank::orderBy('name', 'ASC')->get();

        //Permisos del usuario:
        $editar = Auth::user()->can('banks.edit');
        $eliminar = Auth::user()->can('banks.destroy');

        foreach($data as $row){
            //Permisos para las acciones:
            $row->editar = $editar;
            $row->eliminar = $eliminar;
        }

        return datatables($data)->toJson();
    }

    /**
     * 2. Formulario nuevo banco.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.banco_nuevo'),
            'module' => $this->module,
            'slug' => 'banks'
        ];

        return view('admin.banks.create')->with(compact('header'));     
    }

    /**
     * 3. Guardar nuevo banco.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/banks/create')
            ->withErrors($validator)
            ->withInput();    
        }

        //Slug:
        $slug = Str::slug($request->name);

        $row = new Bank();
        $row->name = $request->name;
        $row->slug = $slug;
        $row->tradename = $request->tradename;
        $row->lei = $request->lei;
        $row->eu_code = $request->eu_code;
        $row->supervisor_code = $request->supervisor_code;
        $row->status = $request->status? true:false;
        $row->save();

        //Generar .json de datos:
        $this->jsonBanks();

        $msg = trans('textos.banco_creado');
        return redirect()->route('banks.edit', $row)->with(compact('msg'));
    }

    /**
     * 4. Editar banco.
     */
    public function edit(Bank $bank){
        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.banco_editar'),
            'module' => $this->module,
            'slug' => 'banks'
        ];

        return view('admin.banks.edit')->with(compact('header', 'bank'));  
    }

    /**
     * 5. Actualizar banco.
     */
    public function update(Request $request, Bank $bank){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/banks/'.$bank->id.'/edit')
            ->withErrors($validator)
            ->withInput();    
        }

        //Slug:
        $slug = Str::slug($request->name);

        $bank->name = $request->name;
        $bank->slug = $slug;
        $bank->tradename = $request->tradename;
        $bank->lei = $request->lei;
        $bank->eu_code = $request->eu_code;
        $bank->supervisor_code = $request->supervisor_code;
        $bank->status = $request->status? true:false;
        $bank->save();

        //Generar .json de datos:
        $this->jsonBanks();

        $msg = trans('textos.banco_updated');
        return redirect()->route('banks.edit', $bank)->with(compact('msg'));
    }

    /**
     * 6. Modificar status de banco.
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){
            $row = Bank::find($id);
            $row->status = $status;
            $row->save();

            //Generar .json de datos:
            $this->jsonBanks();

            $resp = ($row)? true:false;
            return response()->json($resp);
        }
    }

    /**
     * 7. Eliminar banco.
     */
    public function destroy(Bank $bank){
        $bank->delete();   

        $msg = trans('textos.banco_deleted');
        return redirect()->route('banks.index')->with(compact('msg')); 
    }

    /**
     * 8. Generar .json de bancos.
     */
    public function jsonBanks(){
        $data = Bank::orderBy('name', 'ASC')->get();
        $jsondata = json_encode($data);
        file_put_contents(storage_path()."/json/banks.json", $jsondata);
    }
}
