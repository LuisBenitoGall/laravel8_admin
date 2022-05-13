<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//Models:
use App\Models\Group;
use App\Models\IpAccess;

class IpAccessController extends Controller{
    /**
     * 1. Relación de IP's de acceso por empresa.
     * 2. Listado de IP's por empresa.
     * 3. Guardar rango de IP's.
     * 4. Eliminar rango.
     */
    
    private $option = 'acceso_ip';
    private $module = 'users';

    public function __construct(){
        $this->middleware('ip_access_get', ['only' => ['destroy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'ip_start' => 'required|ip',
                'ip_end' => 'required|ip'
            ],[
                'ip_start.ip' => trans('textos.ip_error_formato_aviso'),
                'ip_end.ip' => trans('textos.ip_error_formato_aviso')
            ]);
        }
    }

    /**
     * 1. Relación de IP's de acceso por empresa.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.ip_validas',
            'module' => $this->module,
            'slug' => 'users'
        ]; 

        $groups = Group::where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->orderBy('name', 'ASC')
        ->pluck('name', 'id')
        ->toArray();

        return view('admin.ip_access.index')->with(compact('header', 'groups')); 
    }

    /**
     * 2. Listado de IP's por empresa.
     */
    public function getIpsDT(){
        $data = IpAccess::select('ip_access.id', 'ip_access.ip_start', 'ip_access.ip_end', 'groups.name AS group')
        ->leftJoin('groups', 'ip_access.group_id', '=', 'groups.id')
        ->where('ip_access.company_id', session('currentCompany'))
        ->get();

        return datatables($data)->toJson();
    }

    /**
     * 3. Guardar rango de IP's.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-add-rank');

            return redirect('/admin/ip-access')
                ->withErrors($validator)
                ->withInput();
        }

        $rank = new IpAccess();
        $rank->ip_start = $request->ip_start;
        $rank->ip_end = $request->ip_end;
        $rank->company_id = session('currentCompany');
        $rank->user_id = Auth::user()->id;
        $rank->group_id = $request->group_id;
        $rank->save();

        $msg = trans('textos.rango_guardado');
        return redirect()->route('ip-access.index')->with(compact('msg'));
    }

    /**
     * 4. Eliminar rango.
     */
    public function destroy(IpAccess $range){
        $range->delete();

        $msg = trans('textos.ip_rango_deleted');
        return redirect()->route('ip-access.index')->with(compact('msg'));
    }
}
