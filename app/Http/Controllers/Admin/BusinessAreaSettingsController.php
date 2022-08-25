<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

//Models:
use App\Models\BusinessAreaSettings;

class BusinessAreaSettingsController extends Controller{
    /**
     * 1. Guardar configuración.
     */
    
    /**
     * 1. Guardar configuración.
     */
    public function store(Request $request, $ba_id, $custom_invoice){
        if($request->ajax() && $ba_id){
            //dd($custom_invoice);
            //Creamos...
            if($custom_invoice == 1){
                //Template de factura personalizada. Se crea con el id del proveedor + id de business_area:
                if(!View::exists('/pdf/custom_invoices/invoice_'.session('currentCompany').'_'.$ba_id)){
                    $template = fopen('../resources/views/pdf/custom_invoices/invoice_'.session('currentCompany').'_'.$ba_id.'.blade.php', 'w+');

                    $content = '@php
$numInvoice = $invoice->invoice_num? $invoice->invoice_num:$invoice->id; 
$bg_color = $company_settings->base_color_invoices? $company_settings->base_color_invoices:"#10172C";
@endphp

@include("pdf.partials.header", ["header_title" => ucfirst(trans("textos.factura"))." #".$numInvoice, "company_settings" => $company_settings, "bg_color" => $bg_color])

{{-- Aquí el contenido customizado --}}

@include("pdf.partials.invoice-footer")';

                    fwrite($template, $content);
                    fclose($template);
                }

                $template_name = 'invoice_'.session('currentCompany').'_'.$ba_id;

                $bas = new BusinessAreaSettings();
                $bas->ba_id = $ba_id;
                $bas->provider_id = session('currentCompany');
                $bas->custom_invoice = $template_name;
                $bas->save();
            
            //...o eliminamos:  
            }else{
                BusinessAreaSettings::where('ba_id', $ba_id)
                ->where('provider_id', session('currentCompany'))
                ->delete();
            }   
        }   
    }
    

}
