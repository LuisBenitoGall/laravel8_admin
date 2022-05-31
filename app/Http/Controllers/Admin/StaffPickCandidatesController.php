<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

//Models:
use App\Models\StaffPick;
use App\Models\StaffPickCandidates;
use App\Models\StaffPickObservations;
use App\Models\User;
use App\Models\UserDoc;
use App\Models\UserImage;

class StaffPickCandidatesController extends Controller{
    /**
     * 1. Listado de candidatos por proceso para DataTables.
     * 2. Formulario nuevo candidato.
     * 3. Guardar nuevo candidato.
     * 4. Editar candidato.
     * 5. Actualizar candidato.
     * 6. Eliminar candidato.
     * 7. Guardar imagen.
     * 8. Guardar documento.
     */
    
    private $option = 'seleccion_personal';
    private $module = 'rrhh';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:rrhh');
        $this->middleware('staff_pick_get', ['only' => ['create']]);
        $this->middleware('staff_pick_candidates_get', ['only' => ['edit', 'destroy']]);
        $this->middleware('staff_pick_candidates_post', ['only' => ['update']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación store:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'process_id' => 'required|integer',
                'name' => 'required_without:user_id',
                'surname' => 'required_without:user_id',
                'email' => 'required_without:user_id|email',
                'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:500'
            ]);   

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'id' => 'required|integer',
                'user_id' => 'required|integer',
                'name' => 'required|string|max:75',
                'surname' => 'nullable|string|max:100', 
                'email' => "required|string|email|max:191|unique:users,email,".$data['user_id'].",id,deleted_at,NULL",
                'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:500'
            ],[
                'email.unique' => trans('textos.email_existe')
            ]);
        }
    }

    /**
     * 1. Listado de candidatos por proceso para DataTables.
     */
    public function getCandidatesDT($process){
        $data = StaffPickCandidates::select('staff_pick_candidates.id', 'staff_pick_candidates.created_at', 'staff_pick_candidates.assessment', 'users.id AS user_id', 'users.name', 'users.surname')
        ->join('users', 'staff_pick_candidates.user_id', '=', 'users.id')
        ->where('staff_pick_candidates.staff_pick_id', $process)
        ->where('staff_pick_candidates.status', '0')
        ->groupBy('staff_pick_candidates.user_id')
        ->orderBy('users.name', 'ASC')
        ->get();

        //Permisos del usuario:
        $editar = Auth::user()->can('staff-pick.edit');

        foreach($data as $row){
            $row->alta = $row->created_at? $row->created_at->format($this->languages[session()->get('locale')][4]):'';

            //Permisos para las acciones:
            $row->editar = $editar;
        }

        return datatables($data)->toJson();
    }

    /**
     * 2. Formulario nuevo candidato.
     */
    public function create(StaffPick $process){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.candidato_nuevo',
            'module' => $this->module,
            'slug' => 'staff-pick'
        ]; 

        $languages = $this->languages;

        return view('admin.staff_pick_candidates.create')->with(compact('header', 'process', 'languages'));   
    }

    /**
     * 3. Guardar nuevo candidato.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/staff-pick-candidates/'.$request->process_id.'/create')
                ->withErrors($validator)
                ->withInput();
        }   

        if($request->user_id){
            $user_id = $request->user_id;
        }else{
            //Comprobamos que el candidato no exista previamente: 
            $user = User::select('id')
            ->where('email', $request->email)
            ->first();

            //Si no existe lo creamos:
            if(!$user){
                $user = new User();
                $user->name = $request->name;
                $user->surname = $request->surname;
                $user->email = $request->email;
                $user->save();
            }

            $user_id = $user->id;
        }

        $meeting_date = $request->meeting_date != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->meeting_date):$request->meeting_date:NULL;

        //Creamos candidatura:
        $spc = new StaffPickCandidates();
        $spc->staff_pick_id = $request->process_id;
        $spc->user_id = $user_id;
        $spc->save();

        //Guardamos comentarios:
        $spo = new StaffPickObservations();
        $spo->candidate_id = $spc->id;
        $spo->meeting_date = $meeting_date;
        $spo->observations = $request->observations;
        $spo->save();

        //Guardamos imagen:
        $this->saveImage($request, $user_id);

        //Guardamos documento:
        $this->saveDoc($request, $user_id);
        
        if($request->candidate){
            $msg = trans('textos.comentario_agregado');
            return redirect()->route('staff-pick-candidates.edit', [$request->candidate, 'observations'])->with(compact('msg'));
        }else{
            $msg = trans('textos.candidato_creado');
            return redirect()->route('staff-pick.edit', [$request->process_id, 'candidates'])->with(compact('msg'));
        }
    }

    /**
     * 4. Editar candidato.
     */
    public function edit(StaffPickCandidates $candidate, $tab = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.candidato_editar',
            'module' => $this->module,
            'slug' => 'staff-pick'
        ]; 

        $user = User::find($candidate->user_id);
        $process = StaffPick::find($candidate->staff_pick_id);
        $observations = StaffPickObservations::where('candidate_id', $candidate->id)
        ->orderBy('created_at', 'DESC')
        ->get();        

        $images = $user->images;

        $docs = $user->docs;

        $languages = $this->languages;

        return view('admin.staff_pick_candidates.edit')->with(compact('header', 'candidate', 'tab', 'user', 'process', 'observations', 'images', 'docs', 'languages'));
    }

    /**
     * 5. Actualizar candidato.
     */
    public function update(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/staff-pick-candidates/'.$request->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }  

        $user = User::find($request->user_id);
        $user->name = $request->name;
        $user->surname = $request->surname;
        $user->email = $request->email; 
        $user->save();

        //Actualizamos valoración del candidato:
        StaffPickCandidates::where('id', $request->id)
        ->update(['assessment' => $request->assessment]);

        //Guardamos imagen:
        $this->saveImage($request, $user->id);

        //Guardamos documento:
        $this->saveDoc($request, $user->id);

        $msg = trans('textos.comentario_agregado');
        return redirect()->route('staff-pick-candidates.edit', $request->id)->with(compact('msg'));
    }

    /**
     * 6. Eliminar candidato.
     */
    public function destroy(StaffPickCandidates $candidate){
        //Eliminamos los comentarios sobre el candidato:
        StaffPickObservations::where('candidate_id', $candidate->id)
        ->delete();

        $candidate->delete();

        $msg = trans('textos.candidato_eliminado');
        return redirect()->route('staff-pick.edit', [$candidate->staff_pick_id, 'candidates'])->with(compact('msg'));
    }

    /**
     * 7. Guardar imagen.
     */
    public function saveImage($request, $user_id){
        if($request->hasFile('image')){
            $file = $request->file('image');
            $path = public_path().'/img/users';
            $fileName = uniqid().$file->getClientOriginalName();
            $file->move($path, $fileName);

            $img = new UserImage();
            $img->image = $fileName;
            $img->user_id = $user_id;
            $img->featured = true;
            $img->public = true;
            $img->save();
        }    
    }

    /**
     * 8. Guardar documento.
     */
    public function saveDoc($request, $user_id){
        if($request->hasFile('doc')){
            $file = $request->file('doc');
            $path = public_path().'/docs/users';
            $fileName = uniqid().$file->getClientOriginalName();
            $file->move($path, $fileName);

            $doc = new UserDoc();
            $doc->doc = $fileName;
            $doc->user_id = $user_id;
            $doc->type = 'cv';
            $doc->save();
        }    
    }
}
