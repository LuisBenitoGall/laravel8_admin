<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use File;
use Carbon\Carbon;

//Models:
use App\Models\Company;
use App\Models\Contact;
use App\Models\ContactComment;
use App\Models\Phone;
use App\Models\User;
use App\Models\UserCompanies;

class ContactController extends Controller{
    /**
     * 1. Listado de contactos.
     * 1.1. Listado de contactos para DataTable.
     * 2. Formulario nuevo contacto.
     * 3. Guardar nuevo contacto.
     * 4. Editar contacto.
     * 5. Actualizar contacto.
     * 6. Mostrar contacto.
     * 7. Eliminar contacto.
     */
    
    private $option = 'contactos';
    private $module = 'crm';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:crm');

        // $this->middleware('user_get', ['only' => ['edit', 'update', 'updatePwd', 'storeImage']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación verificación de email:
        if(isset($data['submit_verify'])){
            return Validator::make($data, [
                'contact_email' => 'required|string|email'
            ]);

        //Validación save:
        }elseif(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|string|max:75',
                'surname' => 'nullable|string|max:100',
                'email' => [
                    'nullable',
                    'string',
                    'email',
                    'max:191',
                    Rule::unique('users', 'email')->ignore($data['user_id'] ?? null),
                ]
            ],[
                'email.unique' => trans('textos.email_existe')
            ]);
        }
    }

    /**
     * 1. Listado de contactos.
     */
    public function index(Request $request){
        $related_company = false;
        $status = false;
        
        if(!empty($request->all())){
            $related_company = $request->company_id? Company::find($request->company_id):false;  
            $status = $request->status? $request->status:'';  
        }

        $header = [
            'option' => $this->option,
            'suboption' => trans('textos.contactos_mis'),
            'module' => $this->module,
            'slug' => 'contacts'
        ];

        //Estados de CRM:
        $status_options = helpers::crmStatus();

        return view('admin.contacts.index')->with(compact('header', 'related_company', 'status', 'status_options'));
    }

    /**
     * 1.1. Listado de contactos para DataTable.
     */
    public function getContactsDT(Request $request){
        $related_company = $request->related_company? $request->related_company:false;
        $status = $request->status? $request->status:false;

        //Estados de CRM:
        $status_options = helpers::crmStatus();

        $data = User::select('users.id', 'users.name', 'users.surname', 'users.email', 'contacts.id AS contact_id', 'contacts.position', 'contacts.status', 'contacts.created_at', DB::raw('(SELECT image FROM user_images WHERE user_id = users.id AND featured = 1) AS Image')
        )
        ->join('contacts', 'users.id', '=', 'contacts.user_id')
        ->where('contacts.company_id', session('currentCompany'))
        ->whereNull('contacts.deleted_at')
        ->when($related_company, function($q) use($related_company){
            return $q->where('contacts.related_company_id', $related_company);
        })
        ->when($status, function($q) use($status){
            return $q->where('contacts.status', $status);
        })
        ->groupBy('users.id')
        ->orderBy('users.name', 'ASC');

        return DataTables::of($data)
        ->addColumn('user', function($row){
            return ucwords($row->name).' '.ucwords($row->surname);
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
        ->addColumn('position', function($row){
             return $row->position;
        })
        ->addColumn('status', function($row) use($status_options){
            if($status_options[$row->status]){
                return ucfirst($status_options[$row->status]);
            }else{
                return '';
            }
        })
        ->addColumn('image', function($row){
            if($row->Image){
                return '<img src="'.asset('img/users/'.$row->Image).'" class="img-circle" width="36">';
            }else{
                return helpers::setAvatar($row->name, $row->surname);
            }
        })
        ->addColumn('acciones', function($row){
            return view('admin.contacts.partials.dt-actions', compact('row'));
        })
        ->rawColumns(['email', 'image'])
        ->make(true);
    }

    /**
     * 2. Formulario nuevo contacto.
     */
    public function create(Request $request, $related_company = false){
        if(!empty($request->all())){
            $validator = $this->validator($request->all());
            if($validator->fails()){
                return redirect('admin/contacts/create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        //Verificamos si existe el email:
        $contact_exist = false;
        $contact_email = false;
        if($request->contact_email){
            $contact_exist = User::where('email', $request->contact_email)->first();
            $contact_email = $request->contact_email;
        }

        $related_company = $related_company? Company::find($related_company):($request->related_company? Company::find($request->related_company):false);

        $header = [
            'option' => $this->option,
            'suboption' => trans('textos.contacto_nuevo'),
            'module' => $this->module,
            'slug' => 'contacts'
        ];

        //Estados de CRM:
        $status_options = helpers::crmStatus();

        return view('admin.contacts.create')->with(compact('header', 'related_company', 'contact_exist', 'contact_email', 'status_options'));
    }

    /**
     * 3. Guardar nuevo contacto.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/contacts/create')
                ->withErrors($validator)
                ->withInput();
        }

        //Si no existe el usuario:
        if(!$request->user_id){
            $user = new User();
            $user->name = $request->name;
            $user->surname = $request->surname;
            $user->email = $request->email;
            $user->isAdmin = false;
            $user->save();

            $userId = $user->id;
        
        }else{
            $userId = $request->user_id;
        }

        //Crear contacto:
        $contact = new Contact();
        $contact->company_id = session('currentCompany');
        $contact->related_company_id = $request->company_id;
        $contact->user_id = $userId;
        $contact->position = $request->position;
        $contact->status = $request->status;
        $contact->is_primary = $request->is_primary? true:false;
        $contact->save();

        //Teléfonos:
        $i = 0;
        foreach($request->phone_number as $row){
            if($row != ''){
                $phone = new Phone();
                $phone->user_id = $userId;
                $phone->phone_number = $row;
                $phone->concept = $request->concept[$i];
                $phone->save();
                $i++;
            }
        }

        //Vinculación a empresa:
        if($contact->related_company_id){
            UserCompany::firstOrCreate(
                ['user_id' => $userId, 'company_id' => $contact->related_company_id]
            );    
        }

        $msg = trans('textos.contacto_creado');
        return redirect()->route('contacts.edit', $contact)->with(compact('msg'));
    }

    /**
     * 4. Editar contacto.
     */
    public function edit(Contact $contact, $tab = false){
        $header = [
            'option' => $this->option,
            'suboption' => trans('textos.contacto_editar'),
            'module' => $this->module,
            'slug' => 'contacts'
        ];

        $user = User::find($contact->user_id);

        $related_company = $contact->related_company_id? Company::select('id', 'name', 'tradename')->find($contact->related_company_id):false;

        //Phones:
        $phones = Phone::where('user_id', $user->id)->get(); 

        //Estados de CRM:
        $status_options = helpers::crmStatus();

        //Comentarios:
        $comments = ContactComment::select('contact_comments.*', 'users.name', 'users.surname')
        ->where('contact_comments.contact_id', $contact->id)
        ->join('users', 'contact_comments.author_id', '=', 'users.id')
        ->orderBy('contact_comments.created_at', 'DESC')
        ->get();

        //Categorías:
        $groupedCategories = helpers::crmCategories()['grouped'];
        $flatCategories = helpers::crmCategories()['flat'];

        $languages = $this->languages;

        return view('admin.contacts.edit')->with(compact('header', 'contact', 'tab', 'user', 'related_company', 'phones', 'status_options', 'comments', 'groupedCategories', 'flatCategories', 'languages'));
    }

    /**
     * 5. Actualizar contacto.
     */
    public function update(Request $request, Contact $contact){
        $contact->position = $request->position;
        $contact->status = $request->status;
        $contact->is_primary = $request->is_primary? true:false;
        $contact->save();

        //Teléfonos:
        $userId = $contact->user_id;
        DB::transaction(function () use ($userId, $request){
            Phone::where('user_id', $userId)->delete();

            foreach($request->phone_number as $index => $number){
                if(!empty($number)){
                    Phone::create([
                        'user_id'      => $userId,
                        'phone_number' => $number,
                        'concept'      => $request->concept[$index] ?? null,
                    ]);
                }
            }
        });

        $msg = trans('textos.contacto_actualizado');
        return redirect()->route('contacts.edit', $contact)->with(compact('msg'));
    }

    /**
     * 6. Mostrar contacto.
     */
    public function show(Contact $contact){
        $header = [
            'option' => $this->option,
            'suboption' => trans('textos.contacto_ver'),
            'module' => $this->module,
            'slug' => 'contacts'
        ];

        $user = User::find($contact->user_id);

        return view('admin.contacts.show')->with(compact('header', 'contact', 'user'));
    }

    /**
     * 7. Eliminar contacto.
     */
    public function destroy(Contact $contact){
        $contact->delete();

        $msg = trans('textos.contacto_eliminado');
        return redirect()->route('contacts.index')->with(compact('msg'));
    }
}
