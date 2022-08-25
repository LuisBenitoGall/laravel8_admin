<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use File;
use Carbon\Carbon;

//Models:
use App\Models\BusinessAreas;
use App\Models\Category;
use App\Models\Company;
use App\Models\CostCenters;
use App\Models\Employee;
use App\Models\Phone;
use App\Models\StaffPickCandidates;
use App\Models\Stock;
use App\Models\Timetable;
use App\Models\TimetableItems;
use App\Models\User;
use App\Models\UserBusinessAreas;
use App\Models\UserCompanies;
use App\Models\UserCostCenters;
use App\Models\UserTimetables;
use App\Models\UserWorkplaces;
use App\Models\UserImage;
use App\Models\WorkCalendar;
use App\Models\WorkUserCalendar;
use App\Models\Workplace;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller{
	/**
     * 1. Listado de usuarios.
     * 1.1. Listado de todos los usuarios.
     * 1.2. Listado de usuarios para DataTable.
     * 2. Formulario nuevo usuario.
     * 3. Guardar nuevo usuario.
     * 4. Mostrar usuario.
     * 5. Editar usuario.
     * 6. Actualizar datos personales de usuario.
     * 7. Eliminar firma de usuario.
     * 8. Actualizar contraseña.
     * 9. Guardar imagen.
     * 10. Eliminar imagen.
     * 11. Destacar imagen.
     * 11.1. Imagen pública.
     * 12. Activar / desactivar usuario.
     * 13. Horarios del usuario.
     * 13.1. Horarios de usuario para DataTable.
     * 14. Eliminar usuario.
     * 15. Incorporar candidato como usuario.
     */
    
    private $option = 'usuarios';
    private $module = 'users';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('user_get', ['only' => ['edit', 'update', 'updatePwd', 'storeImage']]);
        //$this->middleware('user_timetables', ['only' => ['userTimetables']]);
        $this->middleware('super_admin_validate', ['only' => ['update']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'roles' => 'required|numeric',
                'name' => 'required|string|max:75',
                'surname' => 'nullable|string|max:100',
                'email' => 'nullable|string|email|max:191|unique:users,email'
                //,NULL,id,deleted_at,NULL
            ],[
                'email.unique' => trans('textos.email_existe')
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|string|max:75',
                'surname' => 'nullable|string|max:100',
                'email' => "nullable|string|email|max:191|unique:users,email,".$data['id'].",id,deleted_at,NULL",
                'nickname' => "nullable|string|max:100|unique:users,nickname,".$data['id'].",id,deleted_at,NULL",
                'nif' => "nullable|string|max:15|unique:users,nif,".$data['id'].",id,deleted_at,NULL",
                'signature' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:500'

                //11/05/21: se elimina indefinidamente la validación de nif |validateNif
            ],[
                'email.unique' => trans('textos.email_existe'),
                'nif.unique' => trans('textos.nif_existe')  
            ]);   

        //Validación update password:
        }elseif(isset($data['update_pwd'])){ 
            return Validator::make($data, [  
                'password' => 'required|string|confirmed|min:6|max:12'  
            ],[
                'password.min' => trans('textos.password_aviso_num_caracteres'),
                'password.max' => trans('textos.password_aviso_num_caracteres')
            ]);

        //Validación imagen:
        }elseif(isset($data['submit_image'])){
            return Validator::make($data, [
                'file' => 'required|image|mimes:jpeg,jpg,png,gif|max:500'
            ]);    
        }
    }

    /**
     * 1. Listado de usuarios.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.usuarios_empresa'), 
            'module' => $this->module,
            'slug' => 'users'
        ];

        $view = 'list';

        $all = false;

        return view('admin.users.index')->with(compact('header', 'view', 'all'));
    }	

    /**
     * 1.1. Listado de todos los usuarios.
     */
    public function usersAll(){
        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.usuarios_todos'), 
            'module' => $this->module,
            'slug' => 'users-all'
        ];

        $users = User::all();

        $view = 'list';

        $all = true;

        return view('admin.users.index')->with(compact('header', 'users', 'view', 'all'));    
    }

    /**
     * 1.2. Listado de usuarios para DataTable.
     */
    public function getUsersDT($all = false){
        $query = User::select('users.*', 'user_companies.categories',
            DB::raw('(SELECT image FROM user_images WHERE user_id = users.id AND featured = 1) AS Image')    
            )
        ->join('user_companies', 'users.id', '=', 'user_companies.user_id');
        if(!$all){
            $query->where('user_companies.company_id', session('currentCompany'));
        }
        $query->groupBy('users.id')
        ->orderBy('users.name');
        $data = $query;

        return DataTables::of($data)
        ->addColumn('user', function($row){
            return $row->name.' '.$row->surname;
        })
        ->addColumn('alta', function($row){
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
        ->addColumn('categories', function($row){
            $categories = '';
            if($row->categories){
                $arr = explode(',', $row->categories);
                $obj_categories = Category::whereIn('id', $arr)
                ->pluck('name')
                ->toArray();

                $categories = implode(', ', $obj_categories);
            }

            return $categories;
        })
        ->addColumn('acciones', function($row){
            return view('admin.users.partials.dt-actions', compact('row'));    
        })
        ->addColumn('image', function($row){
            if($row->Image){
                return '<img src="'.asset('img/users/'.$row->Image).'" class="img-circle" width="36">';    
            }else{
                return helpers::setAvatar($row->name, $row->surname);
            }
        })
        ->rawColumns(['email', 'image'])
        ->make(true);
    }

    /**
     * 2. Formulario nuevo usuario.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.usuario_nuevo'), 
            'module' => $this->module,
            'slug' => 'users-create'
        ];

        $roles = Role::whereNull('company_id')
        ->orWhere('company_id', session('currentCompany'))
        ->get();

        return view('admin.users.create')->with(compact('header', 'roles'));    
    }

    /**
     * 3. Guardar nuevo usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/users/create')
                ->withErrors($validator)
                ->withInput();
        }

        //Comprobamos si el rol dispone de permisos para declarar $isAdmin:
        $role = Role::find($request->input('roles'));

        if(!$role){
            $alert = trans('textos.role_no_existe');
            return redirect()->route('users.create')->with(compact('alert'));  
            exit;  
        }

        $permissions = $role->permissions;

        $isAdmin = false;
        if($role->name == config('constants.SUPER_ADMIN_') || $permissions->count()){
            $isAdmin = true;
        }

        $status = $request->input('status')? 1:0;
        $random_password = Str::random(8);

        $user = new User();
        $user->name = $request->name;
        $user->surname = $request->surname;
        $user->email = $request->email;
        $user->password = bcrypt($random_password);
        $user->isAdmin = $isAdmin;
        $user->status = $status;
        $user->remember_token = $request->input('_token');
        $user->save();

        //Guardamos rol:
        $user->assignRole($role->name);

        //Vinculamos usuario a empresa:
        $uc = new UserCompanies();
        $uc->user_id = $user->id;
        $uc->company_id = session('currentCompany');
        $uc->save();

        //Envío de password:
        if($request->input('send_pwd')){
            $emailFrom = config('constants.EMAIL_');
            $emailTo = $user->email;
            $data['usuario'] = $user->name.' '.$user->surname;
            $data['password'] = $random_password;
            $company = Company::find(session('currentCompany'));
            $data['company'] = $company;

            Mail::send('emails.send-user-password', $data, function($message) use($emailFrom, $company, $emailTo){
                $message->from($emailFrom, $company->name);
                $message->to($emailTo);
                $message->subject(trans('textos.contrasena_envio')); 
            }); 
        }

        $msg = trans('textos.usuario_creado_ok');
        return redirect()->route('users.edit', $user->id)->with(compact('msg'));
    }
    
    /**
     * 4. Mostrar usuario.
     */
    public function show(Request $request, User $user){
        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.usuario_ver'), 
            'module' => $this->module,
            'slug' => 'users-show'
        ];

        //Imágenes:
        $images = UserImage::where('user_id', $user->id)
        ->where('public', 1)
        ->get();

        //Phones:
        $phones = Phone::where('user_id', $user->id)->get();

        $previous_url = url()->previous();
        if(str_contains($previous_url, 'users-all')){
            $return = 'users-all';
        }else{
            $return = 'users';
        }

        return view('admin.users.show')->with(compact('header', 'user', 'images', 'phones', 'return')); 
    }
    
    /**
     * 5. Editar usuario.
     *
     * @param  \App\user  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user, $tab = false, $from = false, $to = false){
        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.usuario_editar'), 
            'module' => $this->module
        ];

        //Roles: según sea Super Admin o no
        if(Auth::user()->hasRole('Super Admin')){
            $roles = Role::whereNull('company_id')
            ->orWhere('company_id', session('currentCompany'))
            ->get();
        }else{
            $roles = Role::where('name', '!=', config('constants.SUPER_ADMIN_'))
            ->whereNull('company_id')
            ->orWhere('company_id', session('currentCompany'))
            ->get();
        }

        //User roles:
        $user_roles = $user->roles;

        //Países:
        $countries = $this->countries(1);

        //Imágenes:
        $images = $user->images;

        $languages = $this->languages;

        //Contrato vigente:
        $employee = Employee::select('employees.id', 'employees.start_date', 'employees.end_date')
        ->join('contracts', 'employees.contract_id', '=', 'contracts.id')
        ->where('employees.user_id', $user->id)
        ->where('contracts.company_id', session('currentCompany'))
        ->first();

        if($employee){
            //Áreas de negocio:
            $business_areas = BusinessAreas::where('company_id', session('currentCompany'))
            ->pluck('name', 'id')
            ->toArray();

            //Áreas de negocio del usuario:
            $user_business_areas = UserBusinessAreas::getUserBusinessAreas($user->id, session('currentCompany'));

            //Centros de coste:
            $cost_centers = CostCenters::where('company_id', session('currentCompany'))
            ->pluck('name', 'id')
            ->toArray();

            //Centros de coste del usuario:
            $user_cost_centers = UserCostCenters::getUserCostCenters($user->id, session('currentCompany'));

            //Centros de trabajo:
            $workplaces = Workplace::where('company_id', session('currentCompany'))
            ->pluck('name', 'id')
            ->toArray();

            //Centros de trabajo del usuario:
            $user_workplaces = UserWorkplaces::getUserWorkplaces($user->id, session('currentCompany'));

        }else{
            $business_areas = false;
            $user_business_areas = false;
            $cost_centers = false;
            $user_cost_centers = false;
            $workplaces = false;
            $user_workplaces = false;
        }

        //Categorías:
        $categories = Category::select('categories.id', 'categories.name')
        ->join('modules', 'categories.module_id', '=', 'modules.id')
        ->where('modules.slug', 'users')
        ->where('categories.company_id', session('currentCompany'))
        ->where('categories.status', 1)
        ->get();

        //Categorías del usuario:
        $user_categories = UserCompanies::select('categories')
        ->where('user_id', $user->id)
        ->where('company_id', session('currentCompany'))
        ->first();

        //Phones:
        $phones = Phone::where('user_id', $user->id)->get();

        //Empleados:
        //$employees = Employee::companyEmployees(session('currentCompany'), 1);

        //Módulos activos:
        $module_timetables = $this->settedModule('timetables'); 
        $module_calendar = $this->settedModule('calendar');
        $module_rrhh = $this->settedModule('rrhh');

        //Material asignado al usuario:
        //$stocks = Stock::stockByUser($user->id);

        //return view('admin.users.edit')->with(compact('header', 'user', 'tab', 'roles', 'user_roles', 'countries', 'images', 'business_areas', 'user_business_areas', 'cost_centers', 'user_cost_centers', 'workplaces', 'user_workplaces', 'categories', 'user_categories', 'phones', 'employees', 'timetables', 'calendar', 'stocks'));
        return view('admin.users.edit')->with(compact('header', 'user', 'tab', 'roles', 'user_roles', 'countries', 'images', 'languages', 'employee', 'business_areas', 'user_business_areas', 'cost_centers', 'user_cost_centers', 
            'workplaces', 'user_workplaces', 'categories', 'user_categories', 'phones', 'module_timetables', 'module_calendar', 'module_rrhh'));    
    }

    /**
     * 6. Actualizar datos personales de usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/users/'.$user->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Guardamos firma:
        $signature = $this->loadUserSignature($request, $user->id);

        $birthday = $request->input('birthday') != ''? config('app.locale') != 'en'? helpers::convertFecha($request->input('birthday')):$request->input('birthday'):NULL;

        //Comprobamos si el rol dispone de permisos para declarar $isAdmin:
        $role = Role::find($request->input('roles'));
        if(!$role){
            $alert = trans('textos.role_no_existe');
            return redirect()->route('users.edit', $user)->with(compact('alert'));  
            exit;  
        }

        $permissions = $role->permissions;

        $isAdmin = false;
        if($role->name == config('constants.SUPER_ADMIN_') || $permissions->count()){
            $isAdmin = true;
        }

        //1º Actualizar user:
        $user = User::find($request->input('id'));
        $user->name = $request->input('name');
        $user->surname = $request->input('surname');
        $user->email = $request->input('email');
        $user->nickname = $request->input('nickname');
        $user->birthday = $birthday;
        $user->nif = $request->input('nif');
        $user->naf = $request->input('naf');
        if($signature){
            $user->signature = $signature;
        }
        $user->isAdmin = $isAdmin;
        $user->save();  

        //Guardamos rol:
        $user->assignRole($role->name);

        //Guardamos categorías del usuario:
        $user_categories = $request->categories? implode(',', $request->categories):false;
        UserCompanies::where('user_id', $user->id)
        ->where('company_id', session('currentCompany'))
        ->update(['categories' => $user_categories]);

        $msg = trans('textos.usuario_updated');
        return redirect()->route('users.edit', $user->id)->with(compact('msg'));
    }

    /**
     * 7. Eliminar firma de usuario.
     *
     * @param  obj $request string $signature
     * @return \Illuminate\Http\Response
     */
    public function deleteSignature(Request $request, $signature){
        if($signature != ''){
            $user = User::select('id', 'signature')->where('signature', $signature)->first();

            $data = User::find($user->id);
            $data->signature = NULL;
            $data->save(); 

            File::delete(public_path().'/img/signatures/'.$signature);

            $msg = trans('textos.imagen_deleted');
            return redirect()->route('users.edit', $user->id)->with(compact('msg'));  
        
        }else{
            return redirect('/');
        } 
    }   

    /**
     * 8. Actualizar contraseña.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePwd(Request $request, User $user){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/users/'.$user->id.'/edit/password')
                ->withErrors($validator)
                ->withInput();
        }

        $user->password = bcrypt($request->input('password'));
        $user->save();

        $msg = trans('textos.password_updated');
        return redirect()->route('users.edit', $user->id)->with(compact('msg'));   
    }

    /**
     * 9. Guardar imagen.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeImage(Request $request, User $user){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/users/'.$user->id.'/edit/images')
                ->withErrors($validator)
                ->withInput();
        }

        if($request->hasFile('file')){
            //Si es la primera imagen, la marcamos como destacada:
            $images = UserImage::select('id')
            ->where('user_id', $user->id)
            ->get();
            $featured = $images->count() == 0? 1:0;

            $file = $request->file('file');
            $path = public_path().'/img/users';
            $fileName = uniqid().$file->getClientOriginalName();
            $moved = $file->move($path, $fileName);

            //si el archivo ha sido guardado correctamente:
            if($moved){
                $img = new UserImage;
                $img->image = $fileName;
                $img->user_id = $user->id;
                $img->featured = $featured;
                $img->save();
            }else{
                return false;
            }

            $msg = trans('textos.imagen_guardada');
            return redirect()->route('users.edit', [$user->id, 'images'])->with(compact('msg'));  
        }    
    }

    /**
     * 10. Eliminar imagen.
     *
     * @param  obj $request string $image
     * @return \Illuminate\Http\Response
     */
    public function deleteImage(Request $request, $image, $return = false){
        if($image != ''){
            $userImage = UserImage::select('id', 'user_id')->where('image', $image)->first();

            $userImage->delete();

            File::delete(public_path().'/img/users/'.$image);

            $msg = trans('textos.imagen_deleted');

            if($return){
                $p = explode('|', $return);
                return redirect()->route($p[0], $p[1])->with(compact('msg'));    
            }else{
                return redirect()->route('users.edit', [$userImage->user_id, 'images'])->with(compact('msg'));  
            }
        
        }else{
            return redirect('/');
        } 
    }  

    /**
     * 11. Destacar imagen.
     *
     * @param  obj $request string $image
     * @return \Illuminate\Http\Response
     */
    public function setFeatured(Request $request, $image){
        if($request->ajax()){ 
            $userImage = UserImage::where('image', $image)->first();
            
            UserImage::where('user_id', $userImage->user_id)
            ->update(['featured' => 0]);

            //Destacamos imagen:
            $userImage->featured = 1;
            $userImage->save();

            return response()->json($userImage);

        }else{
            return redirect('/');
        }
    }

    /**
     * 11.1. Imagen pública.
     *
     * @param  obj $request string $image
     * @return \Illuminate\Http\Response
     */
    public function setPublic(Request $request, $image){
        if($request->ajax()){ 
            $userImage = UserImage::select('id', 'public')->where('image', $image)->first();
           
            $public = $userImage->public? false:true;

            //Publicamos imagen:
            $userImage->public = $public;
            $userImage->save();

            return response()->json($userImage);

        }else{
            return redirect('/');
        }
    }

    /**
     * 12. Activar / desactivar usuario.
     *
     * @param  int $id @status
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $user = User::find($id);
            $user->status = $status;
            $user->save();

            $resp = ($user)? true:false;
            return response()->json($resp);
        }
    }

    /**
     * 13. Horarios del usuario.
     */
    public function userTimetables(Request $request, $user_id = false, $add_register = false){
        //Módulos activos:
        $set_timetables = $this->settedModule('timetables');
        
        if(!$set_timetables){
            $alert = trans('textos.no_permisos_accion');
            return redirect()->route('users.index')->with(compact('alert'));
            exit;
        }   

        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.horarios_empleado'), 
            'module' => $this->module,
            'slug' => 'calendar'
        ];

        $userId = $user_id? $user_id:$request->user_id;

        //Centro de trabajo del empleado vinculado al contrato vigente. Debe existir al menos 1 para mostrar los horarios.
        $contract = Employee::select('workplaces.id', 'workplaces.name', 'employees.id')
        ->join('workplaces', 'employees.workplace_id', '=', 'workplaces.id')
        ->where('employees.user_id', $userId)
        ->where('workplaces.company_id', session('currentCompany'))
        ->where('employees.status', 1)
        ->first();
        
        if(!$contract){
            $alert = trans('textos.centro_trabajo_usuario_alerta');
            return redirect()->route('users.edit', [$userId, 'company'])->with(compact('alert'));
        }

        $user = User::find($userId);

        //La fecha 'desde' se manipula si viene de un registro creado manualmente por el administrador:
        $fromRegistered = $add_register? $add_register:Date('Y-m-d');

        $from = $request->from != ''? (session()->get('locale') != 'en')? Helpers::convertFecha($request->from):$request->from:Date('Y-m-d');
        $to = $request->to != ''? (session()->get('locale') != 'en')? Helpers::convertFecha($request->to):$request->to:Date('Y-m-d');
        //La fecha fin no puede ser posterior a la fecha actual:
        $to = $to > date('Y-m-d')? date('Y-m-d'):$to;

        //Conceptos horarios:
        $obj_timetable_items = TimetableItems::where('company_id', session('currentCompany'))->get();
        $timetable_items = json_encode($obj_timetable_items);

        $languages = $this->languages;

        return view('admin.users.timetables')->with(compact('header', 'user', 'contract', 'from', 'to', 'timetable_items', 'obj_timetable_items', 'languages'));
    }

    /**
     * 13.1. Horarios de usuario para DataTables.
     */
    public function userTimetablesDT($from = false, $to = false, $user_id = false){
        if($from && $to && $user_id){
            //Centro de trabajo del usuario en el intervalo señalado:
            $workplace = UserWorkplaces::getEmployeeWorkplace($user_id, $from, $to);

            $data = Timetable::listUserTimetables($from, $to, $user_id, $workplace->id);

            return DataTables::of($data)
            ->addColumn('fecha', function($row){
                return '<span style="display: none">'.$row['date_order'].'</span>'.$row['fecha'];
            })
            ->addColumn('hour', function($row){
                return $row['hour'];
            })
            ->addColumn('name', function($row){
                return ucfirst($row['name']);
            })
            ->addColumn('accumulated', function($row){
                $accumulated = $row['accumulated'];
                if($row['accumulated'] != '' && $row['hoursOfWork']){
                    $accumulated .= ' / '.$row['hoursOfWork'];   
                }
                if($row['diferencia']){
                    $clase = $row['sign'] == '-'? 'text-danger':'text-success';
                    $accumulated .= ' <strong class="'.$clase.' ml-2">'.$row['diferencia'].'</strong>';
                }
                return $accumulated;
            })
            ->addColumn('comments', function($row){
                return $row['comments'];
            })
            ->addColumn('acciones', function($row){
                return view('admin.users.partials.dt-timetable-actions', compact('row'));
            })
            ->rawColumns(['fecha', 'accumulated'])
            ->make(true);
        
        }else{
            return false;
        }      
    }

    /**
     * 14. Eliminar usuario.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user){
        $user->delete();

        $msg = trans('textos.usuario_deleted');
        return redirect()->route('users.index')->with(compact('msg'));    
    }

    /**
     * 15. Incorporar candidato como usuario.
     */
    public function fromCandidate(User $user){
        $user->status = 1;
        $user->save();

        //Guardamos rol:
        $user->assignRole(config('constants.ROLE_BASICO_'));

        //Vinculamos usuario a empresa:
        $uc = new UserCompanies();
        $uc->user_id = $user->id;
        $uc->company_id = session('currentCompany');
        $uc->save();

        //Eliminamos la condición de candidato:
        StaffPickCandidates::join('staff_picks', 'staff_pick_candidates.staff_pick_id', '=', 'staff_picks.id')
        ->where('staff_pick_candidates.user_id', $user->id)
        ->where('staff_picks.company_id', session('currentCompany'))
        ->update(['staff_pick_candidates.status' => 1]);

        $msg = trans('textos.usuario_incorporado');
        return redirect()->route('users.edit', $user->id)->with(compact('msg'));
    }











    /**
     * . Listado de todos los usuarios para DataTable.
     */
    public function getUsersAllDT(){
        $users = User::select('users.*', 'user_companies.categories',
            DB::raw('(SELECT image FROM user_images WHERE user_id = users.id AND featured = 1) AS Image')    
        )
        ->join('user_companies', 'users.id', '=', 'user_companies.user_id')
        ->groupBy('users.id')
        ->get();

        //Permisos:
        $users_show = Auth::user()->can('users.show');

        //Ampliación de información de los objetos:
        if($users){
            foreach($users as $row){
                //Enviamos las fechas ya formateadas:
                $row->alta = $row->created_at->format('d-m-Y');

                //Permisos para las acciones:
                $row->add_companies = false;
                $row->users_on = false;
                $row->users_edit = false;
                $row->users_show = $users_show;

                //Categorías:
                $arr = $row->categories? explode(',', $row->categories):false;
                if($arr){
                    $row->categories = '';
                    foreach($arr as $cat){
                        $category = Category::select('name')->find($cat);   
                        if($row->categories != ''){
                            $row->categories .= ', ';
                        } 
                        $row->categories .= $category->name;
                    }

                }else{
                    $row->categories = '';
                }

                //Teléfonos:
                $phones = $row->phones;
                $row->phones = false;
                if($phones->count() > 0){
                    $arrPhones = [];
                    foreach($phones as $phone){
                        array_push($arrPhones, $phone->phone_number);
                    }
                    $row->phones = $arrPhones;
                }
            }
        }

        return datatables($users)->toJson(); 
    }
}
