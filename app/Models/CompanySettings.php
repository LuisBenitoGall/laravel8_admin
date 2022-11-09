<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;

class CompanySettings extends Model{
    /**
     * 1. Moneda de la empresa.
     * 2. IVA de la empresa.
     */

    use HasFactory;
    use SoftDeletes;

    protected $table = 'company_settings';

    protected $fillable = ['company_id', 'iva'];

    /**
     * 1. Moneda de la empresa.
     */
    public static function co_currency($index = false, $company_id = false){
        $companyId = $company_id? $company_id:session('currentCompany');

        $data = CompanySettings::select('currencies.name', 'currencies.code', 'currencies.number', 'currencies.symbol')
        ->join('currencies', 'company_settings.currency_id', '=', 'currencies.id')
        ->where('company_settings.company_id', $companyId)
        ->first();

        //En caso de no estar declarado traemos moneda por defecto:
        if(!$data){
            $data = Currency::where('code', config('constants.CURRENCY_NAME_'))->first();
        }

        //Modificación objeto:
        if($data && !$data->symbol){
            //Casos especiales:
            $euro = ['EUR'];
            $dollar = ['USD', 'USN', 'USS'];
            if(in_array($data->code, $euro)){
                $data->symbol = '€';
            }
            if(in_array($data->code, $dollar)){
                $data->symbol = '$';
            }
        }

        return $data;
    }

    /**
     * 2. IVA de la empresa.
     */
    public static function co_iva(){
        $data = CompanySettings::select('iva')
        ->where('company_id', session('currentCompany'))
        ->first();

        $iva = $data? $data->iva:config('constants.IVA_DEFAULT_');
        return $iva;
    }

    public function company(){
        return $this->belongsTo(Company::class);
    }
}
