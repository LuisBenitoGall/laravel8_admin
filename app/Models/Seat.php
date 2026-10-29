<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Seat extends Model{
    /**
     * 1. Generar nuevo asiento contable.
     * 2. Relación con facturas.
     * 3. Efectos contables.
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at']; 

    protected $table = 'seats';

    /**
     * 1. Generar nuevo asiento contable.
     */
    public static function saveSeat($data){
        $seat = new Seat();
        $seat->company_id = $data['company_id'];
        $seat->year = date('Y');
        $seat->seat_num = $data['seat_num']; 
        $seat->seat_type = $data['seat_type']; 
        $seat->seat_date = $data['seat_date'];
        //$seat->operation_type = $data['operation_type']; 
        $seat->diary = $data['diary']; 
        $seat->concept = $data['concept']; 
        $seat->description = $data['description']; 
        //$seat->serie = $data['serie']; 
        $seat->doc_num = $data['doc_num'];
        $seat->doc = $data['doc']; 
        $seat->sort_num = $data['sort_num']; 
        $seat->company_target = $data['company_target']; 
        $seat->mode = $data['mode']; 
        $seat->created_by = Auth::user()->id;
        //$seat->order_id = isset($data['order_id'])? $data['order_id']:NULL;
        $seat->invoice_id = $data['invoice_id'];
        //$seat->cash_id = $data['cash_id'];
        $seat->provider_doc = $data['provider_doc']; 
        $seat->pay_sign = $data['pay_sign']; 
        $seat->save();

        //Importe acumulado de las distintas líneas:
        $accumulated_debit = 0;
        $accumulated_credit = 0;

        //Líneas:
        if(isset($data['seat_lines'])){
            //Numeración líneas:
            $k = 1;
            for($i = 0; $i < count($data['seat_lines']['accounting_account_id']); $i++){
                $accumulated_debit = $accumulated_debit + $data['seat_lines']['debit'][$i]? $data['seat_lines']['debit'][$i]:'0';
                $accumulated_credit = $accumulated_credit + $data['seat_lines']['credit'][$i]? $data['seat_lines']['credit'][$i]:'0';

                $data_line = [
                    'seat_id' => $seat->id,
                    'line_num' => $k,
                    'accounting_account_id' => $data['seat_lines']['accounting_account_id'][$i],
                    'debit' => $data['seat_lines']['debit'][$i],
                    'credit' => $data['seat_lines']['credit'][$i],
                    'code_auto' => NULL,
                    'type' => $data['seat_lines']['type'][$i],
                    'currency_id' => $data['currency_id']
                ];
                $lin = SeatLines::saveLine($data_line);

                $k++;
            }
        }

        //Líneas de IVA:
        if(isset($data['iva_lines'])){
            //Variables para controlar el último registro del loop.
            $total_iva_lines = count($data['iva_lines']);
            $j = 1;

            //foreach($data['iva_lines'] as $iva_line){
            for($i_ = 0; $i_ < count($data['iva_lines']['accounting_account_id']); $i_++){

                // $debit_line = $iva_line['debit'][$i_];
                // $credit_line = $iva_line['credit'][$i_];

                $accumulated_debit = $accumulated_debit + $data['iva_lines']['debit'][$i_]? $data['iva_lines']['debit'][$i_]:'0';
                $accumulated_credit = $accumulated_credit + $data['iva_lines']['credit'][$i_]? $data['iva_lines']['credit'][$i_]:'0';

                //Ajustamos el importe de la última línea para cuadrar los importes:
                if($total_iva_lines == $j && $accumulated_debit != $accumulated_credit){
                    $credit_line = $credit_line + ($accumulated_debit - $accumulated_credit);
                }

                $data_line = [
                    'seat_id' => $seat->id,
                    'line_num' => $k,
                    'accounting_account_id' => $data['iva_lines']['accounting_account_id'][$i_],
                    'debit' => $data['iva_lines']['debit'][$i_],
                    'credit' => $data['iva_lines']['credit'][$i_],
                    'code_auto' => NULL,
                    'type' => $data['iva_lines']['type'][$i_],
                    'currency_id' => $data['currency_id']
                ];
                SeatLines::saveLine($data_line);  
                
                $k++;   //Prosigo con la numeración del loop de líneas. 
                $j++;
            }
        }

        return $seat;        
    }

    /**
     * 2. Relación con facturas.
     */
    public function invoice(){
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    /**
     * 3. Efectos contables.
     */
    public function effects(){
        return $this->belongsToMany(Effect::class, 'seat_effects');    
    }
}
