<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempImportCompaniesTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('temp_import_companies', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('name')->nullable();
            $table->string('tradename')->nullable();
            $table->string('nif')->nullable();
            $table->string('address')->nullable();
            $table->string('cp')->nullable();
            $table->string('town')->nullable();
            $table->string('billing_comunication')->nullable();
            $table->string('pay_after_days')->nullable();
            $table->string('payday')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('temp_import_companies');
    }
}
