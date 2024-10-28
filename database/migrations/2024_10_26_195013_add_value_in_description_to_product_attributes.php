<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddValueInDescriptionToProductAttributes extends Migration{
    /**
     * Run the migrations.
     *
     * Indica si el valor del atributo se incluye en la descripción de la variante de producto.
     *
     * @return void
     */
    public function up(){
        Schema::table('product_attributes', function (Blueprint $table){
            $table->boolean('value_in_description')->default(false)->after('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('product_attributes', function (Blueprint $table){
            $table->dropColumn(['value_in_description']); 
        });
    }
}
