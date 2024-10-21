<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVariantOfToProducts extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('products', function (Blueprint $table){
            $table->bigInteger('variant_of')->nullable()->after('id'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('products', function (Blueprint $table){
            $table->dropColumn(['variant_of']);    
        });
    }
}
