<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;

class Effect extends Model{
    /**
     * 1. Generar nuevo efecto.
     * 2. Empresa vinculada.
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'effect_num',
        'is_group',
        'parent',
        'date',
        'company_id',
        'provider_id',
        'provider_bank_account_id',
        'customer_id',
        'customer_bank_account_id',
        'invoice_id',
        'payment_method_id',
        'payment_status',
        'amount',
        'currency_id',
        'created_by',
        'updated_by',
        'status'
    ];

    /**
     * 1. Generar nuevo efecto.
     */
    public static function setEffect($data){
        //Numeración efecto:
        $num = NULL;

        //Factura:
        $invoice = Invoice::select('id', 'invoice_num', 'invoice_old_num')->find($data['invoice_id']);

        if($data['invoice_id'] != NULL){
            $num = $invoice->invoice_num? $invoice->invoice_num:($invoice->invoice_old_num? $invoice->invoice_old_num:$invoice->id);
            $num = $num.'/01';
        }

        //Por agrupación de facturas:
        if($data['is_group']){
            $num = 'A/'.date('YmdHis');
        }

        //Crear una instancia de la clase actual:
        $instance = new self();

        $instance->effect_num = $num;
        $instance->is_group = $data['is_group'];
        $instance->parent = $data['parent'];
        $instance->date = $data['date'];
        $instance->company_id = session('currentCompany');
        $instance->provider_id = $data['provider_id'];
        $instance->provider_bank_account_id = $data['provider_bank_account_id'];
        $instance->customer_id = $data['customer_id'];
        $instance->customer_bank_account_id = $data['customer_bank_account_id'];
        $instance->invoice_id = $data['is_group']? NULL:$data['invoice_id'];
        //$instance->serie_id = $data['serie_id'];
        $instance->remittance_id = $data['remittance_id'];
        $instance->payment_method_id = $data['payment_method_id'];
        $instance->payment_status = $data['payment_status'];
        $instance->amount = $data['amount'];
        $instance->currency_id = $data['currency_id'];
        $instance->observations = isset($data['observations'])? $data['observations']:NULL;
        $instance->created_by = $data['created_by'];
        $instance->updated_by = $data['updated_by'];
        $instance->status = $data['status'];
        $instance->save();

        //if($data['is_group']){
        // if($invoice){
        //     $invoice->effect_id = $instance->id;
        //     $invoice->save();
        // }

        return $instance;
    }

    /**
     * 2. Empresa vinculada.
     */
    public function company(){
        return $this->belongsTo(Company::class);
    }

}
