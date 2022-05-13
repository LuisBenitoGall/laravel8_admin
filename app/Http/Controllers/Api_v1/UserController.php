<?php

namespace App\Http\Controllers\Api_v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\User;

class UserController extends Controller{
    /**
     * 1. Listado usuarios.
     * 2. Mostrar usuario.
     */
    
    /**
     * 1. Listado usuarios.
     */
    public function index(){
        $data = User::all();

        return $data;
    }

    /**
     * 2. Mostrar usuario.
     */
    public function show(User $user){
        return $user;
    }
}
