<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

//Models:
use App\Models\PaymentMethod;

class PaymentMethodController extends Controller{
    /**
     * 1. Relación de métodos de pago.
     * 1.1. Listado de métodos de pago por empresa para DataTables.
     * 2. Formulario nuevo método.
     * 3. Guardar nuevo método.
     * 4. Editar método.
     * 5. Actualizar método.
     * 6. Actualizar estado.
     * 7. Eliminar método.
     */
    
    private $option = 'contabilidad';
    private $module = 'accounting';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:accounting');
        $this->middleware('payment_methods_get', ['only' => ['edit', 'update', 'destroy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación guardar:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|max:255',
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|max:255',
            ]);
        }
    }

    /**
     * 1. Relación de métodos de pago.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.metodos_pago',
            'module' => $this->module,
            'slug' => 'payment-methods'
        ];  

        return view('admin.payment_methods.index')->with(compact('header'));
    }

    /**
     * 1.1. Listado de métodos de pago por empresa para DataTables.
     */
    public function getPaymentMethodsDT(){
        $data = PaymentMethod::where('company_id', session('currentCompany'))
        ->orderBy('name', 'ASC');

        return DataTables::of($data)
        ->addColumn('acciones', function($row){
            return view('admin.payment_methods.partials.dt-actions', compact('row'));    
        })
        ->make(true);
    }

    /**
     * 2. Formulario nuevo método.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.metodo_nuevo',
            'module' => $this->module,
            'slug' => 'payment-methods'
        ];  

        return view('admin.payment_methods.create')->with(compact('header'));    
    }

    /**
     * 3. Guardar nuevo método.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/payment-methods/create')
                ->withErrors($validator)
                ->withInput();
        }

        $pm = new PaymentMethod();
        $pm->company_id = session('currentCompany');
        $pm->name = $request->name;
        $pm->description = $request->description;
        $pm->status = $request->status? 1:'0';
        $pm->save();

        $msg = trans('textos.metodo_creado');
        return redirect()->route('payment-methods.edit', $pm)->with(compact('msg'));
    }

    /**
     * 4. Editar método.
     */
    public function edit(PaymentMethod $method){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.metodo_editar',
            'module' => $this->module,
            'slug' => 'payment-methods'
        ];  

        return view('admin.payment_methods.edit')->with(compact('header', 'method'));
    }

    /**
     * 5. Actualizar método.
     */
    public function update(Request $request, PaymentMethod $method){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/payment-methods/'.$method->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $method->name = $request->name;
        $method->description = $request->description;
        $method->status = $request->status? 1:'0';
        $method->save();

        $msg = trans('textos.metodo_updated');
        return redirect()->route('payment-methods.edit', $method)->with(compact('msg'));
    }

    /**
     * 6. Actualizar estado.
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $method = PaymentMethod::find($id);
            $method->status = $status;
            $method->save();

            $resp = ($method)? true:false;
            return response()->json($resp);
        }
    }

    /**
     * 7. Eliminar método.
     */
    public function destroy(PaymentMethod $method){
        $method->delete();

        $msg = trans('textos.metodo_deleted');
        return redirect()->route('payment-methods.index')->with(compact('msg'));       
    }
}
