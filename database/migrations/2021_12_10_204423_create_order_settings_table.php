<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderSettingsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('order_settings', function (Blueprint $table){
            $table->id();
            $table->string('name');
            $table->string('slug');
            //FK:
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->text('conditions')->nullable();
            $table->text('payment_method')->nullable();
            $table->text('observations')->nullable();
            $table->text('footer_legend')->nullable();
            $table->integer('featured')->default(false);
            $table->integer('status')->default(true);
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
        Schema::dropIfExists('order_settings');
    }
}
