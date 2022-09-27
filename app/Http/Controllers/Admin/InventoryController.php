<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use File;

class InventoryController extends Controller{
    /**
     * 1. Inventario.
     */
    
    private $option = 'stocks';
    private $module = 'stocks';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();
    }

    /**
     * 1. Inventario.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.inventario',
            'module' => $this->module,
            'slug' => 'inventory'
        ];   

        return view('admin.inventory.index')->with(compact('header')); 
    }
}
