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
use Yajra\DataTables\DataTables;
use File;
use Carbon\Carbon;

//Models:
use App\Models\Contact;
use App\Models\ContactComment;

class ContactCommentController extends Controller{
    /**
     * 1. Guardar comentario.
     * 2. Actualizar comentario.
     * 3. Eliminar comentario.
     */
    
    private $option = 'contactos';
    private $module = 'crm';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:crm');
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'contact_id' => 'required|integer',
                'comment' => 'required|string'
            ]);
        }
    }

    /**
     * 1. Guardar comentario.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-add-comment');

            return redirect('admin/contacts/'.$request->contact_id.'/edit/comments')
            ->withErrors($validator)
            ->withInput();    
        }

        $c = new ContactComment();
        $c->contact_id = $request->contact_id;
        $c->author_id = Auth::user()->id;
        $c->comment = $request->comment;
        $c->category = $request->category;
        $c->tags = $request->tags;
        $c->status = 1;
        $c->save();

        $msg = trans('textos.nota_guardada');
        return redirect()->route('contacts.edit', [$request->contact_id, 'comments'])->with(compact('msg'));
    }

    /**
     * 2. Actualizar comentario.
     */
    public function update(Request $request){
        $comment = ContactComment::find($request->comment_id);
        $comment->comment = $request->comment;
        $comment->save();

        $msg = trans('textos.nota_actualizada');
        return redirect()->route('contacts.edit', [$comment->contact_id, 'comments'])->with(compact('msg'));
    }

    /**
     * 3. Eliminar comentario.
     */
    public function destroy(ContactComment $comment){
        $comment->delete();

        $msg = trans('textos.nota_eliminada');
        return redirect()->route('contacts.edit', [$comment->contact_id, 'comments'])->with(compact('msg'));
    }
}
