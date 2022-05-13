<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use App\Notifications\PurchaseMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use File;
use Carbon\Carbon;

//Models:
use App\Models\Order;

class ReceivingController extends Controller{
    /**
     * 1. Recepción de pedidos.
     * 1.1. Listado de recepciones por DataTable.
     */
    
    private $option = 'recepciones';
    private $module = 'logistics';
    private $languages;
    
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.listado',
            'module' => $this->module,
            'slug' => 'receivings'
        ]; 


        return view('admin.receivings.index')->with(compact('header'));
    }
}
