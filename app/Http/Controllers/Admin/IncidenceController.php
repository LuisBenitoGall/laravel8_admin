<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;

//Models:
use App\Models\CompanySettings;

class IncidenceController extends Controller{
    /**
     * 1. Listado de incidencias.
     */
    
    private $option = 'incidencias';
    private $module = 'incidences';
    private $languages;
    private $co_currency;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        //Moneda:
        $this->co_currency = CompanySettings::co_currency(0);
    }

    /**
     * 1. Listado de incidencias.
     */
    public function index(){
         $header = [
            'option' => $this->option, 
            'suboption' => 'textos.incidencias',
            'module' => $this->module,
            'slug' => 'incidences'
        ]; 

        return view('admin.incidences.index')->with(compact('header'));
    }
}
