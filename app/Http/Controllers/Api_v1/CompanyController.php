<?php

namespace App\Http\Controllers\Api_v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\Company;

class CompanyController extends Controller{
    /**
     * 1. Listado de empresas.
     * 2. Mostrar empresa.
     */
    
    /**
     * 1. Listado de empresas.
     */
    public function index(){
        $data = Company::all();

        return $data;
    }

    /**
     * 2. Mostrar empresa.
     */
    public function show(Company $company){
        return $company;
    }
}
