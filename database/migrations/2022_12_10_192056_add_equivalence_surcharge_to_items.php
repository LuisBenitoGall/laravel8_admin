<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEquivalenceSurchargeToItems extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('items', function (Blueprint $table){
            $table->float('equivalence_surcharge',4,2)->nullable()->after('iva');  //Recargo de equivalencia que acompaña al IVA para algunos sectores.  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('items', function (Blueprint $table){
            $table->dropColumn(['equivalence_surcharge']);
        });
    }
}
