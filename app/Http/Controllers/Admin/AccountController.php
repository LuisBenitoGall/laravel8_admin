<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

//Models:
use App\Models\Account;

class AccountController extends Controller{
    /**
     * 1. Listado de cuentas.
     * 1.1. Listado de cuentas para DataTable.
     * 2. Formulario nueva cuenta.
     * 3. Guardar nueva cuenta.
     * 4. Editar cuenta.
     * 5. Actualizar cuenta.
     * 6. Eliminar cuenta.
     * 7. Actualizar estado.
     */
    
    private $option = 'configuracion';
    private $module = 'settings';

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255'
            ]);
        }
    } 

    /**
     * 1. Listado de cuentas.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.cuentas',
            'module' => $this->module,
            'slug' => 'accounts'
        ];

        return view('admin.accounts.index')->with(compact('header'));
    }   

    /**
     * 1.1. Listado de cuentas para DataTable.
     */
    public function getAccountsDT(){
        return Datatables::of(Account::query())
        ->addColumn('acciones', function($account){
            return view('admin.accounts.partials.dt-actions', compact('account'));    
        })
        ->make(true);
    }

    /**
     * 2. Formulario nueva cuenta.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.cuenta_nueva',
            'module' => $this->module,
            'slug' => 'accounts'
        ];

        return view('admin.accounts.create')->with(compact('header'));
    }

    /**
     * 3. Guardar nueva cuenta.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/accounts/create')
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = Str::slug($request->name);

        $status = $request->status? 1:0;

        $account = Account::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'rate' => $request->rate,
            'duration' => $request->duration,
            'status' => $status  
        ]);

        $msg = trans('textos.cuenta_creado_ok');
        return redirect()->route('accounts.edit', $account->id)->with(compact('msg'));
    }

    /**
     * 4. Editar cuenta.
     */
    public function edit(Account $account){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.cuenta_editar',
            'module' => $this->module,
            'slug' => 'accounts'
        ];

        return view('admin.accounts.edit', compact('header', 'account'));   
    }

    /**
     * 5. Actualizar cuenta.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/account/'.$account->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $status = $request->status? 1:0;

        $account->name = $request->input('name');
        $account->slug = Str::slug($request->input('name'));
        $account->description = $request->input('description');
        $account->rate = $request->input('rate');
        $account->duration = $request->input('duration');
        $account->status = $status;
        $account->save(); 

        $msg = trans('textos.cuenta_updated');
        return redirect()->route('accounts.edit', $account->id)->with(compact('msg'));
    }

    /**
     * 6. Eliminar cuenta.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $account){
        $account_id = $account->id;
        
        $account->delete();  

        $msg = trans('textos.cuenta_deleted');
        return redirect()->route('accounts.index')->with(compact('msg'));   
    }

    /**
     * 7. Actualizar status.
     *
     * @param  int $id @status
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $account = Account::find($id);
            $account->status = $status;
            $account->save();

            $resp = ($account)? true:false;
            return response()->json($resp);
        }
    }
}
