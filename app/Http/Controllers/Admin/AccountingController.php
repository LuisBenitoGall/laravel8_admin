<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;

//Models:
use App\Models\CompanySettings;

class AccountingController extends Controller{
    /**
     * 1. Página principal de contabilidad.
     */
    

    private $option = 'contabilidad';
    private $module = 'accounting';
    private $languages;
    private $co_currency;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        //Moneda:
        $this->co_currency = CompanySettings::co_currency(0);
    }

    /**
     * 1. Página principal de contabilidad.
     */
    public function index(){
         $header = [
            'option' => $this->option, 
            'suboption' => 'textos.contabilidad',
            'module' => $this->module,
            'slug' => 'accounting'
        ]; 

        return view('admin.accounting.index')->with(compact('header'));
    }
}
