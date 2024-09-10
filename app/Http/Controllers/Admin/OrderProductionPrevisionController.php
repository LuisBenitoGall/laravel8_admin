<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//Models:
use App\Models\Order;
use App\Models\OrderProductionPrevision;

class OrderProductionPrevisionController extends Controller{
    /**
     * 1. Eliminar previsión de producción.
     * 2. Copiar registros de order_previsions.
     */
    
    private $option = 'pedidos';
    private $module = 'orders';

    public function __construct(){
        $this->middleware('module_setted:orders');
        //$this->middleware('order_get', ['only' => ['recalculatePrevisions']]);
    }

    /**
     * 1. Eliminar previsión de producción.
     */
    public function destroy(Request $request, OrderProductionPrevision $prevision){
        if($request->ajax()){
            $prevision->forceDelete();

            return response()->json(true);
        }
    }

    /**
     * 2. Copiar registros de order_previsions.
     */
    public function copy(){
        OrderProductionPrevision::copyRegisters();

        dd('hecho');
    }

}
