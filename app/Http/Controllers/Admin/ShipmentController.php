<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

//Models:
use App\Models\CompanySettings;
use App\Models\Shipment;
use App\Models\ShipmentPatterns;

class ShipmentController extends Controller{
    /**
     * 1. Listado de envíos.
     * 1.1. Listado de envíos para DataTable.
     */
    
    private $option = 'envios';
    private $module = 'logistics';
    private $languages;
    private $co_currency;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        //Moneda:
        $this->co_currency = CompanySettings::co_currency(0);
    }

    /**
     * 1. Listado de envíos.
     */
    public function index(){
         $header = [
            'option' => $this->option, 
            'suboption' => 'textos.envios',
            'module' => $this->module,
            'slug' => 'shipments'
        ]; 

        return view('admin.incidences.index')->with(compact('header'));
    }
}
