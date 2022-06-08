<?php

namespace App\Imports;

use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

//Models:
use App\Models\TempImportOrderItems;

class OrderItemsImport implements ToModel, WithHeadingRow{
    protected $sharedId;

    public function __construct($shared_id){
        $this->sharedId = $shared_id;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row){
        $shared_id = $this->sharedId;
        $ref = isset($row['referencia'])? $row['referencia']:NULL;
        $concept = isset($row['concepto'])? $row['concepto']:NULL;
        $qty = isset($row['cantidad'])? $row['cantidad']:'0';
        $price = isset($row['precio'])? $row['precio']:'0';
        $iva = isset($row['iva'])? $row['iva']:config('constants.IVA_DEFAULT_');
        $discount = isset($row['descuento'])? $row['descuento']:NULL;
        $observations = isset($row['observaciones'])? $row['observaciones']:NULL;

        if($shared_id){
            return new TempImportOrderItems([
                'budget_order_id' => $shared_id,
                'ref' => $ref,
                'concept' => $concept,
                'qty' => $qty,
                'price' => $price,
                'iva' => $iva,
                'discount' => $discount,
                'observations' => $observations
            ]);
        } 
    }
}
