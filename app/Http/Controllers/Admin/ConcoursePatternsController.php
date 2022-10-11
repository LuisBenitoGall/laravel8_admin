<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;

//Models:
use App\Models\CompanySettings;

class ConcoursePatternsController extends Controller{
    /**
     * 1. Patrones de referencia de concurso.
     */
    
    private $option = 'presupuestos';
    private $module = 'budgets';
    private $languages;
    private $co_currency;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        //Moneda:
        $this->co_currency = CompanySettings::co_currency(0);
    }

    /**
     * 1. Patrones de referencia de concurso.
     */
    public function index(){
         $header = [
            'option' => $this->option, 
            'suboption' => 'textos.concursos_patrones',
            'module' => $this->module,
            'slug' => 'concourse-patterns'
        ]; 

        return view('admin.concourse_patterns.index')->with(compact('header'));
    }
}
