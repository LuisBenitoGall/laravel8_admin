<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

//Models:
use App\Models\CompanySettings;
use App\Models\TempImportBalances;

class SeatBalancesImport implements ToModel, WithHeadingRow{
    private $co_currency;

    public function __construct(){
        //Moneda:
        $this->co_currency = CompanySettings::co_currency(0);
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row){
        $accounting_account = isset($row['cuenta_contable'])? $row['cuenta_contable']:NULL;
        $debit = isset($row['debe'])? $row['debe']:NULL;
        $credit = isset($row['haber'])? $row['haber']:NULL;
        $type = isset($row['tipo'])? $row['tipo']:NULL;

        if($accounting_account){
            return new TempImportBalances([
                'user_id' => Auth::user()->id,
                'company_id' => session('currentCompany'),
                'accounting_account' => $accounting_account,
                'debit' => $debit,
                'credit' => $credit,
                'type' => $type,
                'currency_id' => $this->co_currency->id
            ]); 
        }   
    }
}
