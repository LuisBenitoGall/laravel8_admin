<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

//Models:
use App\Models\Company;
use App\Models\CompanyAccounts;
use App\Models\User;
use App\Models\UserCompanies;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserCompaniesController extends Controller{
    /**
     * 1. Empresas vinculadas al usuario.
     * 1.1. Listado de empresas vinculadas para DataTable.
     * 2. Vincular - desvincular empresa a usuario.
     * 3. Traspasar empresa a otro usuario.
     * 4. Usuarios por empresa.
     */   
    
    private $option = 'usuarios'; 
    private $module = 'users';

    public function __construct(){
        $this->middleware('user_get', ['only' => ['index']]);
        $this->middleware('user_companies_post', ['only' => ['transferCompany']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['validate_transfer_company'])){
            return Validator::make($data, [
                'company_id' => 'required|numeric',
                'name' => 'required|max:155',
                'surname' => 'required|max:255',
                'email' => 'required|email'
            ],[
                'company_id.required' => trans('textos.empresa_obligatoria')
            ]);
        }
    }

    /**
     * 1. Empresas vinculadas al usuario.
     */
    public function index(User $user){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.empresas_vinculadas',
            'module' => $this->module,
            'slug' => 'users'
        ];

        return view('admin.user_companies.index')->with(compact('header', 'user'));
    }

    /**
     * 1.1. Listado de empresas vinculadas para DataTable.
     */
    public function getCompaniesDT($user = false){
        if(!$user){
            return false;
            exit;
        }   

        //Obtenemos las empresas vinculadas a la empresa en session:
        // $companies = CompanyAccounts::select('companies.id', 'companies.name')
        //     ->join('companies', 'company_accounts.company_id', '=', 'companies.id')
        //     ->where('company_accounts.guardian', session('currentCompany'))
        //     ->where('company_accounts.status', 1)
        //     ->get();
        
        // $companies = UserCompanies::select('companies.id', 'companies.name')
        //     ->join('companies', 'user_companies.company_id', '=', 'companies.id')
        //     ->where('user_companies.user_id', Auth::user()->id)
        //     ->get();
        
        $companies = Auth::user()->companies();

        //Empresas del usuario editado:
        $user_companies = UserCompanies::select('company_id')
        ->where('user_id', $user)
        ->get();

        $num_companies = $user_companies->count();

        //Pasamos empresas de usuario a un array:
        $arr = [];
        foreach($user_companies as $row){
            array_push($arr, $row->company_id);
        }

        //Pasamos al objeto de $companies las que están vinculadas al usuario.
        if($companies){
            foreach($companies as $row){
                $row->activo = in_array($row->id, $arr)? true:false;

                $row->user_id = $user;

                $row->num_companies = $num_companies;
            }
        }

        return datatables($companies)->toJson();
    }

    /**
     * 2. Vincular - desvincular empresa a usuario.
     */
    public function status(Request $request, $company_id = false, $user_id = false, $status){
        if($request->ajax()){
            if(!$company_id || !$user_id){
                return response()->json(false);
            }

            if($status == 1){
                $uc = UserCompanies::where('company_id', $company_id)
                ->where('user_id', $user_id)
                ->delete();

            }else{
                $uc = new UserCompanies();
                $uc->user_id = $user_id;
                $uc->company_id = $company_id;
                $uc->save();
            }

            //Modificamos la session companies si el usuario es el mismo en session abierta:
            if($user_id == Auth::user()->id){
                UserCompanies::userCompanies();
                $response = 'reload';
            }else{
                $response = true;
            }
            
            return response()->json($response);
        }   
    }

    /**
     * 3. Traspasar empresa a otro usuario.
     */
    public function transferCompany(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-transfer-company');

            return redirect('admin/companies')
                ->withErrors($validator)
                ->withInput();
        }

        //Info de la empresa:
        $company = Company::find($request->company_id);

        $random_password = false;

        //Comprobamos si existe el usuario destinatario:
        $user = User::where('email', $request->email)->first();

        //El usuario debe estar activo para poder completar el traspaso:
        if($user && ($user->status == '0' || $user->isAdmin == '0' || $user->deleted_at)){
            $alert = trans('textos.empresa_traspasada_rechazado');
            return redirect()->route('user-companies.index')->with(compact('alert'));   
        }

        //Si no existe se crea:
        if(!$user){
            $random_password = Str::random(8);

            $user = new User();
            $user->name = $request->input('name');
            $user->surname = $request->input('surname');
            $user->email = $request->input('email');
            $user->password = bcrypt($random_password);
            $user->isAdmin = 1;
            $user->status = 1;
            $user->remember_token = $request->input('_token');
            $user->save();
        }

        //Guardamos rol:
        $user->assignRole(config('constants.ROLE_COMPANY_MANAGER_'));

        //Desvinculamos los usuarios actuales de la empresa si la opción ha venido marcada:
        if($request->unlink){
            UserCompanies::where('company_id', $request->company_id)->delete();

            //Si la empresa eliminada es la empresa en sesión, se elimina la sesión de empresa:
            if($request->company_id == session('currentCompany')){
                $request->session()->forget('currentCompany');
            }

            //Regeneramos la session de empresas vinculadas al usuario:
            session()->forget('companies');

            $companies = UserCompanies::select('companies.*')
            ->join('companies', 'user_companies.company_id', '=', 'companies.id')
            ->where('user_companies.user_id', Auth::user()->id)
            ->get();

            foreach($companies as $c){
                \Session::push('companies', $c);
            }
        }

        //Vinculamos usuario a empresa, si aún no lo está:
        $uc = UserCompanies::select('id')
        ->where('user_id', $user->id)
        ->where('company_id', $request->company_id)
        ->first();

        if(!$uc){
            $uc = new UserCompanies();
            $uc->user_id = $user->id;
            $uc->company_id = $request->company_id;
            $uc->save();
        }

        //Envío de confirmación:
        $emailFrom = config('constants.EMAIL_');
        $emailTo = $user->email;
        $data['usuario'] = $user->name.' '.$user->surname;
        $data['password'] = $random_password;
        $data['company'] = $company;
        Mail::send('emails.send-transfer-company-notification', $data, function($message) use($emailFrom, $company, $emailTo){
            $message->from($emailFrom, $company->name);
            $message->to($emailTo);
            $message->subject(trans('textos.empresa_traspaso')); 
        }); 

        $msg = trans('textos.empresa_traspasada_msg');
        return redirect()->route('companies.index')->with(compact('msg'));
    }

    /**
     * 4. Usuarios por empresa.
     */
    public function companyUsers($company_id){
        if($company_id){
            return User::select('users.id', 'users.name', 'users.surname')
            ->join('user_companies', 'users.id', '=', 'user_companies.user_id')
            ->where('user_companies.company_id', $company_id)
            ->where('users.status', 1)
            ->orderBy('users.name', 'ASC')
            ->get();

        }else{
            return false;
        }
    }
}
