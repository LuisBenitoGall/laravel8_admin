<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('tickets', function (Blueprint $table){
            $table->id();
            $table->string('ticket_num')->nullable();
            $table->string('ticket_num_cardinal')->nullable();
            //FK:
            $table->unsignedBigInteger('ticket_pattern_id')->nullable();
            //FK:
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->foreign('provider_id')->references('id')->on('companies');
            //FK: no funciona como clave foránea de una tabla específica, sino que puede tirar del modelo companies o de users:
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('model');    //Valores: companies, users.
            $table->text('observations')->nullable();
            //FK:
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
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
        Schema::dropIfExists('tickets');
    }
}
