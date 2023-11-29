<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Seat extends Model{
    /**
     * 1. Generar nuevo asiento contable.
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
        //$seat->seat_num = $data['seat_num']; 
        $seat->seat_type = $data['seat_type']; 
        $seat->seat_date = $data['seat_date'];
        $seat->operation_type = $data['operation_type']; 
        $seat->diary = $data['diary']; 
        $seat->concept = $data['concept']; 
        $seat->description = $data['description']; 
        //$seat->serie = $data['serie']; 
        $seat->doc_num = $data['doc_num']; 
        $seat->sort_num = $data['sort_num']; 
        $seat->company_target = $data['company_target']; 
        $seat->mode = $data['mode']; 
        $seat->currency_id = $data['currency_id']; 



        $seat->created_by = Auth::user()->id;
        //$seat->order_id = isset($data['order_id'])? $data['order_id']:NULL;
        $seat->invoice_id = $data['invoice_id'];
        //$seat->invoice_date = $data['invoice_date']; 
        //$seat->invoice_num = $data['invoice_num']; 
        //$seat->cash_id = $data['cash_id'];
        $seat->provider_doc = $data['provider_doc']; 
        $seat->pay_sign = $data['pay_sign']; 
        $seat->save();

        //Líneas:
        if(isset($data['seat_lines'])){
            //Numeración líneas:
            $k = 1;
            for($i = 0; $i < count($data['seat_lines']['accounting_account_id']); $i++){
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
                SeatLines::saveLine($data_line);

                $k++;
            }
        }

        return $seat;        
    }
}
