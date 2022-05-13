<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

//Models:
use App\Models\Currency;

class CurrencyController extends Controller{
    /**
     * 1. Listado de monedas.
     * 1.1. Listado de monedas para DataTable.
     * 2. Guardar nueva moneda.
     * 3. Actualizar moneda.
     * 4. Eliminar moneda.
     * 5. Actualizar estado.
     */
    
    private $option = 'configuracion';
    private $module = 'settings';

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255',
                'code' => 'nullable|string|max:3|unique:currencies,code,NULL,id,deleted_at,NULL',
                'number' => 'nullable|max:4|unique:currencies,number,NULL,id,deleted_at,NULL',
                'symbol' => 'nullable|max:2'
            ],[
                'code.unique' => trans('textos.codigo_existe'), 
                'number.unique' => trans('textos.numero_existe')
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => "required|string|max:255",
                'symbol' => 'nullable|max:2'
            ]);
        }
    } 

    /**
     * 1. Listado de monedas.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.monedas',
            'module' => $this->module,
            'slug' => 'currencies'
        ];

        //Importación de monedas desde .json. Se ejecuta 1 vez.
        Currency::import();

        return view('admin.currencies.index')->with(compact('header')); 
    }

    /**
     * 1.1. Listado de monedas para DataTable.
     */
    public function getCurrenciesDT(){
        $currencies = Currency::all();

        //Permisos del usuario:
        $editar = Auth::user()->can('currencies.edit');
        $eliminar = Auth::user()->can('currencies.destroy');

        foreach($currencies as $row){
            //Permisos para las acciones:
            $row->editar = $editar;
            $row->eliminar = $eliminar; 
        }

        return datatables($currencies)->toJson();
    }

    /**
     * 2. Guardar nueva moneda.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'save');

            return redirect('/admin/currencies')
                ->withErrors($validator)
                ->withInput();
        }

        $slug = Str::slug($request->input('name'));
        $status = $request->input('status')? 1:0;

        Currency::create([
            'name' => $request->input('name'),
            'slug' => $slug,
            'code' => $request->input('code'),
            'number' => $request->input('number'), 
            'symbol' => $request->input('symbol'),
            'status' => $status 
        ]); 

        $msg = trans('textos.moneda_creada');
        return redirect()->route('currencies.index')->with(compact('msg'));
    }

    /**
     * 3. Actualizar moneda.
     */
    public function update(Request $request, Currency $currency){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'edit');

            return redirect('/admin/currencies')
                ->withErrors($validator)
                ->withInput();
        }

        $slug = Str::slug($request->input('name'));

        $currency->name = $request->input('name');
        $currency->slug = $slug;
        $currency->symbol = $request->input('symbol');
        $currency->save();

        $msg = trans('textos.moneda_updated');
        return redirect()->route('currencies.index')->with(compact('msg'));
    }

    /**
     * 4. Eliminar moneda.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Currency $currency){
        $currency->delete();  

        $msg = trans('textos.moneda_deleted');
        return redirect()->route('currencies.index')->with(compact('msg'));   
    }

    /**
     * 5. Actualizar estado.
     *
     * @param  int $id @status
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $currency = Currency::find($id);
            $currency->status = $status;
            $currency->save();

            $resp = ($currency)? true:false;
            return response()->json($resp);
        }
    }

}
