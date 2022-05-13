<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

//Models:
use App\Models\Note;

class NoteController extends Controller{
    /**
     * 1. Guardar nota.
     */
    
    private $languages;
    
    public function __construct(){
        $this->languages = Helpers::languages();
    }

    /**
     * 1. Guardar nota.
     */
    public function store(Request $request){
        if($request->ajax()){
            $note = Note::create([
                'user_id' => Auth::user()->id,
                'title' => $request->title,
                'note' => $request->note    
            ]);

            $note->fecha = $note->created_at->format($this->languages[session()->get('locale')][4].' H:i:s');

            return response()->json($note);
        }else{
            return response()->json(false);
        }    
    }
}
