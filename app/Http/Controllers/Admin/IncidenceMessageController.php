<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use File;

//Models:
use App\Models\CompanySettings;
use App\Models\Incidence;
use App\Models\IncidenceDoc;
use App\Models\IncidenceMessage;

class IncidenceMessageController extends Controller{
    /**
     * 1. Listado de mensajes para DataTable.
     * 2. Guardar nuevo mensaje.
     * 3. Leer mensaje.
     * 4. Eliminar mensaje.
     */
    
    private $option = 'incidencias';
    private $module = 'quality';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:quality');
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación create:
        if(isset($data['submit_save'])) {
            return Validator::make($data, [
                'incidence_id' => 'required|integer',
                'message' => 'required'
            ]);
        }
    }

    /**
     * 1. Listado de mensajes para DataTable.
     */
    public function getMessagesDT($incidence_id){
        $data = IncidenceMessage::select('incidence_messages.*', 'users.name AS nombre', 'users.surname')
        ->leftJoin('users', 'incidence_messages.user_id', '=', 'users.id')
        ->where('incidence_messages.incidence_id', $incidence_id)
        ->whereNull('incidence_messages.response_to')
        ->orderBy('incidence_messages.created_at', 'DESC');

        return DataTables::of($data)
        ->addColumn('date', function ($row){
            return $row->created_at->format($this->languages[session()->get('locale')][4].' H:i:s'); 
        })
        ->addColumn('message', function($row){
            return $row->message;
        })
        ->addColumn('corrective_action', function($row){
            if($row->corrective_action){
                return '<i class="fa fa-check text-success is-corrective"></i>';
            }else{
                return '';
            }
        })
        ->addColumn('responses', function($row){
            $responses = IncidenceMessage::where('thread', $row->id)->count();
            if($responses > 0){
                return '<a href="/admin/incidence-messages/'.$row->id.'/show/responses" class="badge badge-warning">'.$responses.'</a>';
            }else{
                return '';
            }
        })
        ->addColumn('author', function($row){
            return $row->nombre.' '.$row->surname;
        })
        ->addColumn('docs', function($row){
            $docs_ = '';
            $docs = $row->docs;
            if($docs->count()){
                foreach($docs as $d){
                    $docs_ .= '<a target="_blank" href="/img/incidences/'.$d->doc.'">doc-'.$d->id.'</a> / ';
                }
            }
            return $docs_;
        })
        ->addColumn('acciones', function($row){
            return view('admin.incidence_messages.partials.dt-actions', compact('row'));    
        })
        ->rawColumns(['message', 'corrective_action', 'responses', 'docs'])
        ->make(true);
    }

    /**
     * 2. Guardar nuevo mensaje.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            $validator->getMessageBag()->add('accion', 'modal-new-message');

            return redirect('/admin/incidences/'.$request->incidence_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }  

        //Hilo:
        $thread = $request->response_to; 

        //Buscamos respuestas al mismo mensaje:
        $response = $request->response_to? IncidenceMessage::select('id', 'thread')->find($request->response_to):false;

        if($response){
            if($response->thread){
                $thread = $response->thread;
            }else{
                $thread = $response->id;
            }    
        }

        $msg = new IncidenceMessage();
        $msg->response_to = $request->response_to;
        $msg->thread = $thread;
        $msg->incidence_id = $request->incidence_id;
        $msg->user_id = Auth::user()->id;
        $msg->message = $request->message;
        $msg->corrective_action = $request->corrective_action? true:false;
        $msg->status = 1;
        $msg->save();
        
        //Documentos:
        //Array de imágenes:
        if($request->docs && $request->docs != ''){
            $arr = explode(',', $request->docs);

            foreach($arr as $i){
                if($i > 0){
                    IncidenceDoc::where('id', $i)
                    ->update(['incidence_message_id' => $msg->id]);
                }
            }
        }

        $msg = trans('textos.mensaje_creado_ok');
        return redirect()->route('incidences.edit', [$request->incidence_id, 'reel'])->with(compact('msg'));
    }

    /**
     * 3. Leer mensaje.
     */
    public function show(IncidenceMessage $message, $tab = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.mensaje_leer',
            'module' => $this->module,
            'slug' => 'incidences'
        ]; 

        $incidence = Incidence::find($message->incidence_id);

        $messages = IncidenceMessage::where('response_to', $message->id)->orderBy('created_at', 'DESC')->get();

        $response = $message->response_to? IncidenceMessage::find($message->response_to):false;

        $languages = $this->languages;

        return view('admin.incidence_messages.show')->with(compact('header', 'message', 'tab', 'incidence', 'messages', 'response', 'languages'));
    }

    /**
     * 4. Eliminar mensaje.
     */
    public function destroy(IncidenceMessage $message){
        //Desvinculamos los documentos del mensaje:
        IncidenceDoc::where('incidence_message_id', $message->id)
        ->update(['incidence_message_id' => NULL]);

        $message->delete();

        $msg = trans('textos.mensaje_deleted');
        return redirect()->route('incidences.edit', [$message->incidence_id, 'reel'])->with(compact('msg'));
    }
}
