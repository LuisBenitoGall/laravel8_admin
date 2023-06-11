<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use File;
use PDF;
use Carbon\Carbon;

//Models:
use App\Models\Group;
use App\Models\Protocol;
use App\Models\ProtocolItem;
use App\Models\ProtocolUser;
use App\Models\User;

class ProtocolController extends Controller{
    /**
     * 1. Protocolos por empresa.
     * 1.1. Listado de protocolos para DataTable.
     * 2. Formulario nuevo protocolo.
     * 3. Guardar nuevo protocolo.
     * 4. Editar protocolo.
     * 5. Actualizar protocolo.
     * 6. Actualizar estado de protocolo.
     * 7. Eliminar protocolo.
     * 8. Imprimir.
     */
    
    private $option = 'protocolos';
    private $module = 'rrhh';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:rrhh');
        $this->middleware('protocol_get', ['only' => ['edit', 'show', 'update', 'destroy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required'
            ]);    

        //Validación update: 
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required',
                'status' => 'required|integer'
            ]); 
        }
    }

    /**
     * 1. Protocolos por empresa.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.listado',
            'module' => $this->module,
            'slug' => 'protocols'
        ]; 

        return view('admin.protocols.index')->with(compact('header'));
    }

    /**
     * 1.1. Listado de protocolos para DataTable.
     */
    public function getProtocolsDT(){
        $data = Protocol::select('protocols.*', 'users.name AS nombre', 'users.surname')
        ->leftJoin('users', 'protocols.created_by', '=', 'users.id')
        ->where('protocols.company_id', session('currentCompany'))
        ->orderBy('protocols.name', 'ASC');

        $estados = helpers::protocolStatus();

        return DataTables::of($data)
        ->addColumn('created_at', function($row){
            return $row->created_at->format($this->languages[session()->get('locale')][4].' H:i:s');
        })
        ->addColumn('author', function($row){
            return $row->nombre.' '.$row->surname;
        })
        ->addColumn('estado', function($row) use($estados){
            return view('admin.protocols.partials.dt-status', compact('row', 'estados'));     
        })
        ->addColumn('acciones', function($row) use($estados){
            return view('admin.protocols.partials.dt-actions', compact('row', 'estados'));    
        })
        ->make(true);
    }

    /**
     * 2. Formulario nuevo protocolo.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.protocolo_nuevo',
            'module' => $this->module,
            'slug' => 'protocols'
        ]; 

        return view('admin.protocols.create')->with(compact('header'));    
    }

    /**
     * 3. Guardar nuevo protocolo.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/protocols/create')
                ->withErrors($validator)
                ->withInput();
        }

        $p = new Protocol();
        $p->company_id = session('currentCompany');
        $p->name = $request->name;
        $p->slug = Str::slug($request->name);
        $p->description = $request->description;
        $p->created_by = Auth::user()->id;
        $p->status = config('constants.PROTOCOL_STATUS_DRAFT_');
        $p->save();

        $msg = trans('textos.protocolo_creado');
        return redirect()->route('protocols.edit', $p)->with(compact('msg'));
    }
    
    /**
     * 4. Editar protocolo.
     */
    public function edit(Protocol $protocol, $tab = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.protocolo_editar',
            'module' => $this->module,
            'slug' => 'protocols'
        ]; 

        $estados = helpers::protocolStatus();

        $author = User::select('id', 'name', 'surname')->find($protocol->created_by);

        //Obtenemos array de los usuarios ya asignados al protocolo, para no repetirlos:
        $users_in = ProtocolUser::where('protocol_id', $protocol->id)
        ->pluck('user_id')
        ->toArray();

        $users = User::select(
            DB::raw("CONCAT(users.name,' ',users.surname) AS user"), 'users.id')
        ->join('user_companies', 'users.id', '=', 'user_companies.user_id')
        ->where('users.status', 1)
        ->where('users.isAdmin', 1)
        ->where('user_companies.company_id', session('currentCompany'))
        ->whereNotIn('users.id', $users_in)
        ->orderBy('users.name')
        ->pluck('user', 'users.id')
        ->toArray();

        //Grupos:
        $groups = Group::where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->orderBy('name')
        ->pluck('name', 'id')
        ->toArray();

        $languages = $this->languages;

        return view('admin.protocols.edit')->with(compact('header', 'protocol', 'tab', 'estados', 'author', 'users', 'groups', 'languages'));  
    }
    
    /**
     * 5. Actualizar protocolo.
     */
    public function update(Request $request, Protocol $protocol){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/protocols/'.$protocol->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $protocol->name = $request->name;
        $protocol->slug = Str::slug($request->name);
        $protocol->description = $request->description;
        $protocol->status = $request->status;
        $protocol->save();

        $msg = trans('textos.protocolo_updated');
        return redirect()->route('protocols.edit', $protocol)->with(compact('msg'));
    }

    /**
     * 6. Actualizar estado de protocolo.
     */
    public function status(Request $request, Protocol $protocol, $status){
        if($request->ajax()){    
            $protocol->status = $status;
            $protocol->save();

            return response()->json(true);
        }
    }

    /**
     * 7. Eliminar protocolo.
     */
    public function destroy(Protocol $protocol){
        $protocol->delete();
        
        $msg = trans('textos.protocolo_deleted');
        return redirect()->route('protocols.index')->with(compact('msg'));    
    }

    /**
     * 8. Imprimir.
     */
    public function print(Protocol $protocol){
        if(!$protocol){
            return redirect('/');
            exit;   
        }    

        $items = ProtocolItem::select('protocol_items.*', 'users.name', 'users.surname')
        ->where('protocol_items.protocol_id', $protocol->id)
        ->leftJoin('users', 'protocol_items.created_by', '=', 'users.id')
        ->orderBy('protocol_items.sort', 'ASC')
        ->get();



        //Título .pdf:
        $pdf_title = $protocol->slug;

        $pdf = PDF::loadView('pdf/protocol', compact('protocol', 'items'));
        return $pdf->download($pdf_title.'.pdf');
    }
}
