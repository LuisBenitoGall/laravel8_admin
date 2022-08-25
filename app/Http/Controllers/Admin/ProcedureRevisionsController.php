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
use File;
use PDF;

//Models:
use App\Models\Procedure;
use App\Models\ProcedureRevisions;

class ProcedureRevisionsController extends Controller{
    /**
     * 1. Guardar revisión.
     * 2. Imprimir revisión.
     */
    
    private $option = 'procedimientos';
    private $module = 'procedures';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:procedures');
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save']) || isset($data['submit_new_version'])){
            return Validator::make($data, [
                'procedure_id' => 'required|integer',
                'content' => 'required'
            ]);
        }
    }

    /**
     * 1. Guardar revisión.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/procedures/'.$request->procedure_id.'/edit/revisions')
            ->withErrors($validator)
            ->withInput();    
        }     

        //Obtenemos el orden a partir del número de revisiones creadas.
        $last_revision = ProcedureRevisions::select('order')
        ->where('procedure_id', $request->procedure_id)
        ->orderBy('order', 'DESC')
        ->first();

        $order = $last_revision != NULL? $last_revision->order + 1:'0';

        // Creamos versión:
        if($request->submit_new_version || !$request->revision_id){
            $rev = new ProcedureRevisions();
            $rev->order = $order;
            $rev->procedure_id = $request->procedure_id;
            $rev->content = $request->content;
            $rev->created_by = Auth::user()->id;
            $rev->updated_by = Auth::user()->id;
            $rev->save();

        //Actualizamos versión:
        }else{
            $rev = ProcedureRevisions::find($request->revision_id);
            $rev->content = $request->content;
            $rev->updated_by = Auth::user()->id;
            $rev->save();
        }

        $msg = trans('textos.revision_creada');
        return redirect()->route('procedures.edit', [$request->procedure_id, 'revisions', $rev->id])->with(compact('msg'));
    }

    /**
     * 2. Imprimir revisión.
     */
    public function print(ProcedureRevisions $revision){
        $procedure = Procedure::find($revision->procedure_id);

        $company_settings = session('companySettings');

        $pdf = PDF::loadView('pdf/procedure', compact('revision', 'procedure', 'company_settings'));
        return $pdf->download($procedure->slug.'.pdf');
    }
}
