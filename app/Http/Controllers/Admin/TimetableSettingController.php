<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

//Models:
use App\Models\TimetableSetting;

class TimetableSettingController extends Controller{
    /**
     * 1. Actualizar configuración.
     */
    
    private $option = 'horarios'; 
    private $module = 'timetables';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:timetables');
    }

    /**
     * 1. Actualizar configuración.
     */
    public function update(Request $request){
        $settings = TimetableSetting::firstOrCreate(
            ['company_id' => session('currentCompany')]
        );

        //Formateando fechas:
        $md_from = $request->md_from != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->md_from):$request->md_from:NULL;
        $md_to = $request->md_to != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->md_to):$request->md_to:config('constants.UNDEFINED_DATE_');

        $settings->minute_discount = $request->minute_discount;
        $settings->md_from = $md_from;
        $settings->md_to = $md_to; 
        $settings->timetable_doc_message = $request->message;       
        $settings->save();

        $msg = trans('textos.configuracion_updated');
        return redirect()->route('timetable-items.index')->with(compact('msg'));
    }
}
