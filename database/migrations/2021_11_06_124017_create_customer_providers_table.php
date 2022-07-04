<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerProvidersTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('customer_providers', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('companies');
            //FK:
            $table->unsignedBigInteger('provider_id');
            $table->foreign('provider_id')->references('id')->on('companies');
            $table->string('acronym')->nullable();
            $table->integer('pay_after_days')->nullable();
            $table->integer('payday')->nullable();
            //FK:
            $table->unsignedBigInteger('ba_id')->nullable();
            $table->foreign('ba_id')->references('id')->on('business_areas');
            //FK:
            $table->unsignedBigInteger('workplace_id')->nullable();
            $table->foreign('workplace_id')->references('id')->on('workplaces');
            //FK:
            $table->unsignedBigInteger('cc_id')->nullable();
            $table->foreign('cc_id')->references('id')->on('cost_centers');
            $table->float('discount', 4, 2)->nullable();
            $table->float('credit_risk', 11, 2)->nullable();     //Riesgo crediticio del cliente.
            $table->text('billing_comunication')->nullable();
            //FK:
            $table->unsignedBigInteger('budget_form_id')->nullable();
            $table->foreign('budget_form_id')->references('id')->on('budget_forms');
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('customer_providers');
    }
}
