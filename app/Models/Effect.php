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
     * 3. Facturas de un efecto.
     * 4. Efectos remesables.
     * 5. Asientos contables.
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
        'payment_method_id',
        'payment_status',
        'amount',
        'currency_id',
        'type',
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
            $num = $num.'/'.$data['numerador'];
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
        
        //Vincular efecto con factura:
        if($data['invoice_id']){
            $ie = new InvoiceEffect();
            $ie->invoice_id = $data['invoice_id'];
            $ie->effect_id = $instance->id;
            $ie->save();
        }

        //Vincular efecto con asiento contable:
        if(isset($data['seat_id']) && $data['seat_id']){
            SeatEffect::create([
                'seat_id' => $data['seat_id'],
                'effect_id' => $instance->id
            ]);
        }

        return $instance;
    }

    /**
     * 2. Empresa vinculada.
     */
    public function company(){
        return $this->belongsTo(Company::class);
    }

    /**
     * 3. Facturas de un efecto.
     */
    public function invoices(){
        return $this->belongsToMany(Invoice::class, 'invoice_effects');
    }

    /**
     * 4. Efectos remesables.
     */
    public static function getEffectsToRemittance($type){
        return Effect::select('effects.id', 'effects.effect_num', 'effects.parent', 'effects.customer_id', 'effects.provider_id', 'effects.date', 'effects.amount', 'effects.status', 'effects.type', 'bank_accounts.account', 'banks.name AS bank', 'companies.name AS company', 'currencies.symbol')
        ->when($type, function($q) use($type){
            //Cobros:
            if($type == 'collection'){
                return $q->where('effects.provider_id', session('currentCompany'));
            }else{
                return $q->where('effects.customer_id', session('currentCompany'));     
            }
        })
        ->join('bank_accounts', 'effects.provider_bank_account_id', '=', 'bank_accounts.id')
        ->join('banks', 'bank_accounts.bank_id', '=', 'banks.id')
        ->when($type, function($q) use($type){
            //Cobros:
            if($type == 'collection'){
                return $q->join('companies', 'effects.customer_id', '=', 'companies.id');
            }else{
                return $q->join('companies', 'effects.provider_id', '=', 'companies.id');
            }
        })
        ->join('currencies', 'effects.currency_id', '=', 'currencies.id')
        ->where('effects.status', config('constants.EFFECT_STATUS_PENDENT_'))
        ->whereNull('effects.remittance_id')
        ->orderBy('effects.created_at', 'DESC')
        ->get();    
    }

    /**
     * 5. Asientos contables.
     */
    public function seats(){
        return $this->belongsToMany(Seat::class, 'seat_effects');
    }

}
