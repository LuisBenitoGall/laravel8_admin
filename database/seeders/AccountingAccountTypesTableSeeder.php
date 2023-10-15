<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Helpers\helpers;

//Models:
use App\Models\AccountingAccountType;

class AccountingAccountTypesTableSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        $types = helpers::accountingAccountsTypes();
        $subtypes = helpers::accountingAccountsSubtypes();

        //Tipos:
        foreach($types as $k => $type){
            $aat = new AccountingAccountType();
            $aat->code = $k;
            $aat->name = $type;
            $aat->save();          
        }

        //Subtipos:
        foreach($subtypes as $k_ => $subtype){
            foreach($subtype as $j_ => $suboption){
                $aat = new AccountingAccountType();
                $aat->autoreference = $k_;
                $aat->code = $j_;
                $aat->name = $suboption;
                $aat->save();      
            }    
        }
    }
}
