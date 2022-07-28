<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyUtesTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('company_utes', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('ute_id');
            $table->foreign('ute_id')->references('id')->on('companies');
            //FK:
            $table->unsignedBigInteger('member_id');
            $table->foreign('member_id')->references('id')->on('companies');
            $table->dateTime('start_at')->nullable();
            $table->dateTime('finish_at')->nullable();
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
        Schema::dropIfExists('company_utes');
    }
}
