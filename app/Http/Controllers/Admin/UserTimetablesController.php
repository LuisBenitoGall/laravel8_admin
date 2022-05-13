<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\UserTimetables;

class UserTimetablesController extends Controller{
    /**
     * 1. Actualizar horario de usuario.
     */
    
    private $option = 'horarios';
    private $module = 'timetables';
    private $languages;

    public function __construct(){
        $this->middleware('module_setted:timetables');
    }

    /**
     * 1. Actualizar horario de usuario.
     */
    public function update(Request $request){
        dd($request->all());
    }

}
