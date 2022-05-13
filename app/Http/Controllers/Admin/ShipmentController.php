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
}
