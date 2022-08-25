<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessAreaSettingsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('business_area_settings', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('ba_id');
            $table->foreign('ba_id')->references('id')->on('business_areas');
            //FK:
            $table->unsignedBigInteger('provider_id');
            $table->foreign('provider_id')->references('id')->on('companies');
            $table->string('custom_invoice')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('business_area_settings');
    }
}
