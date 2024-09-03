<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

//Models:
use App\Models\Dealer;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class DealerController extends Controller{
    /**
     * 1. Lista de dealers.
     * 1.1. Lista de dealers para DataTable.
     * 2. Formulario nuevo repartidor.
     * 3. Guardar nuevo repartidor.
     * 4. Editar repartidor.
     * 5. Mostrar repartidor.
     * 6. Actualizar repartidor.
     * 7. Actualizar estado de repartidor.
     * 8. Eliminar repartidor.
     */
    
    private $option = 'repartidores';
    private $module = 'logistics';
    private $languages;

    public function __construct(){
        $this->languages = helpers::languages();

        $this->middleware('module_setted:logistics');
        $this->middleware('dealer_get', ['only' => ['edit', 'update', 'destroy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación create y update:
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . ($data['id'] ?? ''),
            'phone' => 'nullable|max:20',
            'address' => 'nullable|max:255',
        ]);
    }

    /**
     * 1. Lista de dealers.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.repartidores_lista',
            'module' => $this->module,
            'slug' => 'dealers'
        ]; 

        return view('admin.dealers.index')->with(compact('header'));
    }

    /**
     * 1.1. Lista de dealers para DataTable.
     */
    public function getDealersDT(){
        $data = User::select('users.id', 'users.name', 'users.surname', 'users.email', 'dealers.id AS dealer_id', 'dealers.status', 'dealers.created_at', DB::raw('(SELECT image FROM user_images WHERE user_id = users.id AND featured = 1) AS Image'))
        ->join('dealers', 'users.id', '=', 'dealers.user_id')
        ->where('dealers.company_id', session('currentCompany'))
        ->whereNull('dealers.deleted_at')
        ->orderBy('users.name', 'ASC');

        return DataTables::of($data)
        ->addColumn('user', function($row){
            return ucwords($row->name).' '.ucwords($row->surname);
        })
        ->addColumn('created_at', function($row){
            return $row->created_at->format($this->languages[session()->get('locale')][4]);
        })
        ->addColumn('email', function($row){
             return '<a target="_blank" href="mailto:'.$row->email.'">'.$row->email.'</a>';
        })
        ->addColumn('phones', function($row){
            $phones = '';
            $data_phones = $row->phones;
            $i = 0;
            if($data_phones->count() > 0){
                foreach($data_phones as $p){
                    if($i > 0){
                        $phones .= ' - ';
                    }
                    $phones .= $p->phone_number;
                    $i++;
                }
            }
            return $phones;
        })
        ->addColumn('image', function($row){
            if($row->Image){
                return '<img src="'.asset('img/users/'.$row->Image).'" class="img-circle" width="36">';
            }else{
                return helpers::setAvatar($row->name, $row->surname);
            }
        })
        ->addColumn('acciones', function($row){
            return view('admin.dealers.partials.dt-actions', compact('row'));    
        })
        ->rawColumns(['email', 'image'])
        ->make(true);
    }

    /**
     * 2. Formulario nuevo repartidor.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.repartidor_nuevo',
            'module' => $this->module,
            'slug' => 'dealers'
        ]; 

        $roles = Role::whereNull('company_id')
        ->orWhere('company_id', session('currentCompany'))
        ->orderBy('id', 'ASC')
        ->get();

        return view('admin.dealers.create')->with(compact('header', 'roles'));
    }

    /**
     * 3. Guardar nuevo repartidor.
     *
     * Se guarda en UserController y se incluye en la tabla dealers.
     */
    public function store(Request $request){
        
    }

    /**
     * 4. Editar repartidor.
     *
     * Se edita en UserController.
     */
    public function edit(Dealer $dealer){
        
    }

    /**
     * 5. Mostrar repartidor.
     */
    public function show(Dealer $dealer){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.repartidor_ver',
            'module' => $this->module,
            'slug' => 'dealers'
        ]; 

        $user = User::find($dealer->user_id);

        $languages = $this->languages;

        return view('admin.dealers.show')->with(compact('header', 'dealer', 'user', 'languages'));    
    }

    /**
     * 6. Actualizar repartidor.
     *
     * Se actualiza en UserController.
     */
    public function update(Request $request, Dealer $dealer){
        
    }

    /**
     * 7. Actualizar estado de repartidor.
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){  
            Dealer::where('id', $id)
            ->update(['status' => $status]);

            return response()->json(true);
        }
    }

    /**
     * 8. Eliminar repartidor.
     */
    public function destroy(Dealer $dealer){
        $dealer->delete();
        
        $msg = trans('textos.repartidor_deleted');
        return redirect()->route('dealers.index')->with(compact('msg'));    
    }
}
